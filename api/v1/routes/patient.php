<?php
/**
 * Patient App Routes
 * All endpoints require patient authentication
 */

if ($user['role'] !== 'patient') {
    ApiResponse::unauthorized("Access denied. Patient only.");
}

$patientId = $auth->getEntityId($user['user_id'], 'patient');

switch ($action) {
    // =====================================================
    // DASHBOARD
    // =====================================================
    case 'dashboard':
        $today = date('Y-m-d');

        // Upcoming appointments
        $stmt = $pdo->prepare("SELECT a.*, u_d.name as dentist_name, d.specialization, t.name as treatment_name FROM appointments a JOIN dentists d ON a.dentist_id = d.id JOIN users u_d ON d.user_id = u_d.id JOIN treatments t ON a.treatment_id = t.id WHERE a.patient_id = ? AND a.appointment_date >= ? AND a.status IN ('confirmed', 'pending') ORDER BY a.appointment_date, a.start_time LIMIT 5");
        $stmt->execute([$patientId, $today]);
        $upcoming = $stmt->fetchAll();

        // Recent prescriptions
        $stmt = $pdo->prepare("SELECT pr.*, u_d.name as dentist_name FROM prescriptions pr JOIN dentists d ON pr.dentist_id = d.id JOIN users u_d ON d.user_id = u_d.id WHERE pr.patient_id = ? ORDER BY pr.created_at DESC LIMIT 3");
        $stmt->execute([$patientId]);
        $prescriptions = $stmt->fetchAll();

        // Stats
        $stmt = $pdo->prepare("SELECT COUNT(*) as c FROM appointments WHERE patient_id = ? AND status = 'completed'");
        $stmt->execute([$patientId]);
        $completedAppts = $stmt->fetch()['c'];

        $stmt = $pdo->prepare("SELECT COUNT(*) as c FROM appointments WHERE patient_id = ? AND appointment_date >= ?");
        $stmt->execute([$patientId, $today]);
        $upcomingCount = $stmt->fetch()['c'];

        ApiResponse::success([
            'upcoming_appointments' => $upcoming,
            'recent_prescriptions' => $prescriptions,
            'stats' => [
                'completed_appointments' => $completedAppts,
                'upcoming_appointments' => $upcomingCount
            ]
        ]);
        break;

    // =====================================================
    // FIND DENTISTS
    // =====================================================
    case 'dentists':
        $page = max(1, (int)($input['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        $search = $input['search'] ?? null;
        $specialization = $input['specialization'] ?? null;

        $where = "d.status = 'active'";
        $params = [];

        if ($search) {
            $where .= " AND (u.name LIKE ? OR d.specialization LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($specialization) {
            $where .= " AND d.specialization = ?";
            $params[] = $specialization;
        }

        $countStmt = $pdo->prepare("SELECT COUNT(*) as c FROM dentists d JOIN users u ON d.user_id = u.id WHERE $where");
        $countStmt->execute($params);
        $total = $countStmt->fetch()['c'];

        $params[] = $perPage;
        $params[] = $offset;
        $stmt = $pdo->prepare("SELECT d.id, d.user_id, u.name, u.email, u.phone, u.profile_image, d.specialization, d.qualification, d.experience_years, d.consultation_fee, d.bio, d.working_days, d.working_start_time, d.working_end_time FROM dentists d JOIN users u ON d.user_id = u.id WHERE $where ORDER BY u.name LIMIT ? OFFSET ?");
        $stmt->execute($params);
        $dentists = $stmt->fetchAll();

        ApiResponse::paginated($dentists, $total, $page, $perPage);
        break;

    // =====================================================
    // APPOINTMENTS
    // =====================================================
    case 'appointments':
        if ($method === 'GET') {
            $page = max(1, (int)($input['page'] ?? 1));
            $perPage = 20;
            $offset = ($page - 1) * $perPage;
            $status = $input['status'] ?? null;
            $history = $input['history'] ?? false;

            $where = "a.patient_id = ?";
            $params = [$patientId];

            if ($status) {
                $where .= " AND a.status = ?";
                $params[] = $status;
            }
            if ($history) {
                $where .= " AND a.status IN ('completed', 'cancelled', 'no_show')";
            } else {
                $where .= " AND a.appointment_date >= CURDATE() AND a.status IN ('confirmed', 'pending')";
            }

            $countStmt = $pdo->prepare("SELECT COUNT(*) as c FROM appointments a WHERE $where");
            $countStmt->execute($params);
            $total = $countStmt->fetch()['c'];

            $params[] = $perPage;
            $params[] = $offset;
            $stmt = $pdo->prepare("SELECT a.*, u_d.name as dentist_name, d.specialization, t.name as treatment_name FROM appointments a JOIN dentists d ON a.dentist_id = d.id JOIN users u_d ON d.user_id = u_d.id JOIN treatments t ON a.treatment_id = t.id WHERE $where ORDER BY a.appointment_date DESC, a.start_time DESC LIMIT ? OFFSET ?");
            $stmt->execute($params);
            $appointments = $stmt->fetchAll();

            ApiResponse::paginated($appointments, $total, $page, $perPage);
        } elseif ($method === 'POST') {
            // Book appointment
            $required = ['dentist_id', 'treatment_id', 'appointment_date', 'start_time', 'end_time'];
            foreach ($required as $field) {
                if (empty($input[$field])) ApiResponse::error("Missing: $field");
            }

            // Check availability
            $stmt = $pdo->prepare("SELECT COUNT(*) as c FROM appointments WHERE dentist_id = ? AND appointment_date = ? AND status IN ('pending', 'confirmed') AND ((start_time <= ? AND end_time >= ?) OR (start_time <= ? AND end_time >= ?) OR (start_time >= ? AND start_time <= ?))");
            $stmt->execute([
                $input['dentist_id'], $input['appointment_date'],
                $input['end_time'], $input['start_time'],
                $input['start_time'], $input['end_time'],
                $input['start_time'], $input['end_time']
            ]);
            if ($stmt->fetch()['c'] > 0) {
                ApiResponse::error("Time slot not available");
            }

            $stmt = $pdo->prepare("INSERT INTO appointments (patient_id, dentist_id, treatment_id, appointment_date, start_time, end_time, status, notes, created_by) VALUES (?, ?, ?, ?, ?, ?, 'pending', ?, ?)");
            $stmt->execute([
                $patientId, $input['dentist_id'], $input['treatment_id'],
                $input['appointment_date'], $input['start_time'], $input['end_time'],
                $input['notes'] ?? null, $user['user_id']
            ]);

            ApiResponse::success(['id' => $pdo->lastInsertId()], "Appointment booked");
        }
        break;

    // =====================================================
    // SINGLE APPOINTMENT (Reschedule/Cancel)
    // =====================================================
    case 'appointment':
        $apptId = $parts[2] ?? null;
        if (!$apptId) ApiResponse::error("Appointment ID required");

        if ($method === 'PUT') {
            $newStatus = $input['status'] ?? null;
            $stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ? AND patient_id = ?");
            $stmt->execute([$newStatus, $apptId, $patientId]);
            ApiResponse::success(null, "Appointment updated");
        } elseif ($method === 'DELETE') {
            $stmt = $pdo->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ? AND patient_id = ?");
            $stmt->execute([$apptId, $patientId]);
            ApiResponse::success(null, "Appointment cancelled");
        }
        break;

    // =====================================================
    // MEDICAL RECORDS
    // =====================================================
    case 'medical-records':
        if ($method === 'GET') {
            $stmt = $pdo->prepare("SELECT mr.*, u_d.name as dentist_name FROM medical_records mr JOIN dentists d ON mr.dentist_id = d.id JOIN users u_d ON d.user_id = u_d.id WHERE mr.patient_id = ? ORDER BY mr.created_at DESC");
            $stmt->execute([$patientId]);
            ApiResponse::success($stmt->fetchAll());
        }
        break;

    // =====================================================
    // PRESCRIPTIONS
    // =====================================================
    case 'prescriptions':
        $page = max(1, (int)($input['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $stmt = $pdo->prepare("SELECT COUNT(*) as c FROM prescriptions WHERE patient_id = ?");
        $stmt->execute([$patientId]);
        $total = $stmt->fetch()['c'];

        $stmt = $pdo->prepare("SELECT pr.*, u_d.name as dentist_name FROM prescriptions pr JOIN dentists d ON pr.dentist_id = d.id JOIN users u_d ON d.user_id = u_d.id WHERE pr.patient_id = ? ORDER BY pr.created_at DESC LIMIT $perPage OFFSET $offset");
        $stmt->execute([$patientId]);
        $prescriptions = $stmt->fetchAll();

        // Get medicines for each prescription
        foreach ($prescriptions as &$rx) {
            $medStmt = $pdo->prepare("SELECT * FROM prescription_medicines WHERE prescription_id = ?");
            $medStmt->execute([$rx['id']]);
            $rx['medicines'] = $medStmt->fetchAll();
        }

        ApiResponse::paginated($prescriptions, $total, $page, $perPage);
        break;

    // =====================================================
    // PAYMENTS
    // =====================================================
    case 'payments':
        if ($method === 'GET') {
            $stmt = $pdo->prepare("SELECT p.*, i.invoice_number, i.total_amount FROM payments p JOIN invoices i ON p.invoice_id = i.id WHERE p.patient_id = ? ORDER BY p.payment_date DESC");
            $stmt->execute([$patientId]);
            ApiResponse::success($stmt->fetchAll());
        } elseif ($method === 'POST') {
            // Process online payment
            $required = ['invoice_id', 'amount', 'gateway'];
            foreach ($required as $field) {
                if (empty($input[$field])) ApiResponse::error("Missing: $field");
            }

            $gateway = $input['gateway'];
            $amount = $input['amount'];
            $invoiceId = $input['invoice_id'];

            // Record payment
            $stmt = $pdo->prepare("INSERT INTO payments (invoice_id, patient_id, amount, payment_method, recorded_by, notes) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$invoiceId, $patientId, $amount, $gateway, $user['user_id'], $input['notes'] ?? null]);
            $paymentId = $pdo->lastInsertId();

            // Record transaction
            $stmt = $pdo->prepare("INSERT INTO payment_transactions (payment_id, gateway, amount, status) VALUES (?, ?, ?, 'pending')");
            $stmt->execute([$paymentId, $gateway, $amount]);

            // Update invoice paid amount
            $stmt = $pdo->prepare("UPDATE invoices SET paid_amount = paid_amount + ? WHERE id = ?");
            $stmt->execute([$amount, $invoiceId]);

            // Check if fully paid
            $stmt = $pdo->prepare("SELECT total_amount, paid_amount FROM invoices WHERE id = ?");
            $stmt->execute([$invoiceId]);
            $inv = $stmt->fetch();
            if ($inv && $inv['paid_amount'] >= $inv['total_amount']) {
                $stmt = $pdo->prepare("UPDATE invoices SET status = 'paid' WHERE id = ?");
                $stmt->execute([$invoiceId]);
            }

            ApiResponse::success(['payment_id' => $paymentId], "Payment recorded");
        }
        break;

    // =====================================================
    // TREATMENT PROGRESS
    // =====================================================
    case 'treatment-progress':
        $planId = $input['plan_id'] ?? null;
        if ($planId) {
            $stmt = $pdo->prepare("SELECT tp.*, tpi.* FROM treatment_plans tp LEFT JOIN treatment_plan_items tpi ON tp.id = tpi.plan_id WHERE tp.patient_id = ? AND tp.id = ? ORDER BY tpi.step_number");
            $stmt->execute([$patientId, $planId]);
        } else {
            $stmt = $pdo->prepare("SELECT tp.*, (SELECT COUNT(*) FROM treatment_plan_items WHERE plan_id = tp.id AND status = 'completed') as completed_steps, (SELECT COUNT(*) FROM treatment_plan_items WHERE plan_id = tp.id) as total_steps FROM treatment_plans tp WHERE tp.patient_id = ? ORDER BY tp.created_at DESC");
            $stmt->execute([$patientId]);
        }
        ApiResponse::success($stmt->fetchAll());
        break;

    // =====================================================
    // NOTIFICATIONS
    // =====================================================
    case 'notifications':
        $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 50");
        $stmt->execute([$user['user_id']]);
        ApiResponse::success($stmt->fetchAll());
        break;

    // =====================================================
    // PROFILE
    // =====================================================
    case 'profile':
        if ($method === 'GET') {
            $stmt = $pdo->prepare("SELECT u.name, u.email, u.phone, u.profile_image, p.* FROM users u JOIN patients p ON u.id = p.user_id WHERE p.id = ?");
            $stmt->execute([$patientId]);
            ApiResponse::success($stmt->fetch());
        } elseif ($method === 'PUT') {
            $allowed = ['name', 'phone', 'profile_image', 'gender', 'date_of_birth', 'blood_group', 'address', 'emergency_contact_name', 'emergency_contact_phone', 'allergies', 'medical_conditions'];
            $userUpdates = [];
            $patientUpdates = [];
            $userParams = [];
            $patientParams = [];

            foreach ($allowed as $field) {
                if (isset($input[$field])) {
                    if (in_array($field, ['name', 'phone', 'profile_image'])) {
                        $userUpdates[] = "$field = ?";
                        $userParams[] = $input[$field];
                    } else {
                        $patientUpdates[] = "$field = ?";
                        $patientParams[] = $input[$field];
                    }
                }
            }

            if (!empty($userUpdates)) {
                $userParams[] = $user['user_id'];
                $stmt = $pdo->prepare("UPDATE users SET " . implode(', ', $userUpdates) . " WHERE id = ?");
                $stmt->execute($userParams);
            }
            if (!empty($patientUpdates)) {
                $patientParams[] = $patientId;
                $stmt = $pdo->prepare("UPDATE patients SET " . implode(', ', $patientUpdates) . " WHERE id = ?");
                $stmt->execute($patientParams);
            }

            ApiResponse::success(null, "Profile updated");
        }
        break;

    default:
        ApiResponse::error("Invalid patient endpoint: $action", 404);
}
