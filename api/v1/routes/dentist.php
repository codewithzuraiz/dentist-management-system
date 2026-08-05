<?php
/**
 * Dentist App Routes
 * All endpoints require dentist authentication
 */

if ($user['role'] !== 'dentist') {
    ApiResponse::unauthorized("Access denied. Dentist only.");
}

$dentistId = $auth->getEntityId($user['user_id'], 'dentist');

switch ($action) {
    // =====================================================
    // DASHBOARD
    // =====================================================
    case 'dashboard':
        $today = date('Y-m-d');

        // Today's appointments
        $stmt = $pdo->prepare("SELECT a.*, u_patient.name as patient_name, t.name as treatment_name FROM appointments a JOIN patients p ON a.patient_id = p.id JOIN users u_patient ON p.user_id = u_patient.id JOIN treatments t ON a.treatment_id = t.id WHERE a.dentist_id = ? AND a.appointment_date = ? ORDER BY a.start_time");
        $stmt->execute([$dentistId, $today]);
        $todayAppts = $stmt->fetchAll();

        // Stats
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM appointments WHERE dentist_id = ? AND status != 'cancelled'");
        $stmt->execute([$dentistId]);
        $totalAppts = $stmt->fetch()['total'];

        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM appointments WHERE dentist_id = ? AND DATE(appointment_date) = ? AND status = 'completed'");
        $stmt->execute([$dentistId, $today]);
        $completedToday = $stmt->fetch()['total'];

        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM patients p JOIN appointments a ON p.id = a.patient_id WHERE a.dentist_id = ?");
        $stmt->execute([$dentistId]);
        $totalPatients = $stmt->fetch()['total'];

        ApiResponse::success([
            'today_appointments' => $todayAppts,
            'stats' => [
                'today_completed' => $completedToday,
                'today_total' => count($todayAppts),
                'total_appointments' => $totalAppts,
                'total_patients' => $totalPatients
            ]
        ]);
        break;

    // =====================================================
    // APPOINTMENTS
    // =====================================================
    case 'appointments':
        $page = max(1, (int)($input['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        $status = $input['status'] ?? null;
        $date = $input['date'] ?? null;

        $where = "a.dentist_id = ? AND a.status != 'cancelled'";
        $params = [$dentistId];

        if ($status) {
            $where .= " AND a.status = ?";
            $params[] = $status;
        }
        if ($date) {
            $where .= " AND a.appointment_date = ?";
            $params[] = $date;
        }

        $countStmt = $pdo->prepare("SELECT COUNT(*) as c FROM appointments a WHERE $where");
        $countStmt->execute($params);
        $total = $countStmt->fetch()['c'];

        $params[] = $perPage;
        $params[] = $offset;
        $stmt = $pdo->prepare("SELECT a.*, u_patient.name as patient_name, p.gender as patient_gender, p.phone as patient_phone, t.name as treatment_name FROM appointments a JOIN patients p ON a.patient_id = p.id JOIN users u_patient ON p.user_id = u_patient.id JOIN treatments t ON a.treatment_id = t.id WHERE $where ORDER BY a.appointment_date DESC, a.start_time DESC LIMIT ? OFFSET ?");
        $stmt->execute($params);
        $appointments = $stmt->fetchAll();

        ApiResponse::paginated($appointments, $total, $page, $perPage);
        break;

    // =====================================================
    // UPDATE APPOINTMENT STATUS
    // =====================================================
    case 'appointment':
        $apptId = $parts[2] ?? null;
        $apptAction = $parts[3] ?? null;

        if (!$apptId) {
            ApiResponse::error("Appointment ID required");
        }

        if ($method === 'PUT') {
            $newStatus = $input['status'] ?? null;
            if (!$newStatus) {
                ApiResponse::error("Status required");
            }
            $stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ? AND dentist_id = ?");
            $stmt->execute([$newStatus, $apptId, $dentistId]);
            ApiResponse::success(null, "Appointment updated");
        }
        break;

    // =====================================================
    // PATIENTS LIST
    // =====================================================
    case 'patients':
        $page = max(1, (int)($input['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        $search = $input['search'] ?? null;

        $where = "1=1";
        $params = [];

        if ($search) {
            $where = "(u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $countStmt = $pdo->prepare("SELECT COUNT(*) as c FROM patients p JOIN users u ON p.user_id = u.id WHERE $where");
        $countStmt->execute($params);
        $total = $countStmt->fetch()['c'];

        $params[] = $perPage;
        $params[] = $offset;
        $stmt = $pdo->prepare("SELECT p.id, p.user_id, u.name, u.email, u.phone, p.gender, p.date_of_birth, p.blood_group, p.address, p.status, p.registration_date FROM patients p JOIN users u ON p.user_id = u.id WHERE $where ORDER BY u.name LIMIT ? OFFSET ?");
        $stmt->execute($params);
        $patients = $stmt->fetchAll();

        ApiResponse::paginated($patients, $total, $page, $perPage);
        break;

    // =====================================================
    // PATIENT MEDICAL HISTORY
    // =====================================================
    case 'patient':
        $patientId = $parts[2] ?? null;
        $subAction = $parts[3] ?? null;

        if (!$patientId) ApiResponse::error("Patient ID required");

        if ($subAction === 'history' || $subAction === '') {
            // Medical records
            $stmt = $pdo->prepare("SELECT * FROM medical_records WHERE patient_id = ? ORDER BY created_at DESC");
            $stmt->execute([$patientId]);
            $records = $stmt->fetchAll();

            // Prescriptions
            $stmt = $pdo->prepare("SELECT pr.*, u_d.name as dentist_name FROM prescriptions pr JOIN dentists d ON pr.dentist_id = d.id JOIN users u_d ON d.user_id = u_d.id WHERE pr.patient_id = ? ORDER BY pr.created_at DESC");
            $stmt->execute([$patientId]);
            $prescriptions = $stmt->fetchAll();

            // Treatment notes
            $stmt = $pdo->prepare("SELECT tn.*, u_d.name as dentist_name, t.name as treatment_name FROM treatment_notes tn JOIN dentists d ON tn.dentist_id = d.id JOIN users u_d ON d.user_id = u_d.id LEFT JOIN appointments a ON tn.appointment_id = a.id LEFT JOIN treatments t ON a.treatment_id = t.id WHERE tn.patient_id = ? ORDER BY tn.created_at DESC");
            $stmt->execute([$patientId]);
            $treatmentNotes = $stmt->fetchAll();

            // Appointments history
            $stmt = $pdo->prepare("SELECT a.*, t.name as treatment_name FROM appointments a JOIN treatments t ON a.treatment_id = t.id WHERE a.patient_id = ? ORDER BY a.appointment_date DESC");
            $stmt->execute([$patientId]);
            $appointments = $stmt->fetchAll();

            ApiResponse::success([
                'medical_records' => $records,
                'prescriptions' => $prescriptions,
                'treatment_notes' => $treatmentNotes,
                'appointments' => $appointments
            ]);
        }
        break;

    // =====================================================
    // TREATMENT NOTES
    // =====================================================
    case 'treatment-notes':
        if ($method === 'POST') {
            $required = ['appointment_id', 'patient_id', 'title'];
            foreach ($required as $field) {
                if (empty($input[$field])) ApiResponse::error("Missing: $field");
            }

            $stmt = $pdo->prepare("INSERT INTO treatment_notes (appointment_id, dentist_id, patient_id, title, notes, diagnosis, procedure_done, medications_prescribed, follow_up_date, follow_up_notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $input['appointment_id'], $dentistId, $input['patient_id'],
                $input['title'], $input['notes'] ?? null, $input['diagnosis'] ?? null,
                $input['procedure_done'] ?? null, $input['medications_prescribed'] ?? null,
                $input['follow_up_date'] ?? null, $input['follow_up_notes'] ?? null
            ]);
            ApiResponse::success(['id' => $pdo->lastInsertId()], "Treatment notes saved");
        } elseif ($method === 'GET') {
            $page = max(1, (int)($input['page'] ?? 1));
            $perPage = 20;
            $offset = ($page - 1) * $perPage;

            $stmt = $pdo->prepare("SELECT COUNT(*) as c FROM treatment_notes WHERE dentist_id = ?");
            $stmt->execute([$dentistId]);
            $total = $stmt->fetch()['c'];

            $stmt = $pdo->prepare("SELECT tn.*, u_p.name as patient_name FROM treatment_notes tn JOIN patients pt ON tn.patient_id = pt.id JOIN users u_p ON pt.user_id = u_p.id WHERE tn.dentist_id = ? ORDER BY tn.created_at DESC LIMIT $perPage OFFSET $offset");
            $stmt->execute([$dentistId]);
            $notes = $stmt->fetchAll();

            ApiResponse::paginated($notes, $total, $page, $perPage);
        }
        break;

    // =====================================================
    // PRESCRIPTIONS
    // =====================================================
    case 'prescription':
        if ($method === 'POST') {
            $required = ['patient_id', 'medicines'];
            foreach ($required as $field) {
                if (empty($input[$field])) ApiResponse::error("Missing: $field");
            }

            $prescriptionNumber = 'RX-' . date('Y') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

            $stmt = $pdo->prepare("INSERT INTO prescriptions (prescription_number, patient_id, dentist_id, appointment_id, notes, status) VALUES (?, ?, ?, ?, ?, 'active')");
            $stmt->execute([
                $prescriptionNumber, $input['patient_id'], $dentistId,
                $input['appointment_id'] ?? null, $input['notes'] ?? null
            ]);
            $prescriptionId = $pdo->lastInsertId();

            // Add medicines
            $medStmt = $pdo->prepare("INSERT INTO prescription_medicines (prescription_id, medicine_name, dosage, frequency, duration, instructions) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($input['medicines'] as $med) {
                $medStmt->execute([
                    $prescriptionId, $med['name'], $med['dosage'],
                    $med['frequency'], $med['duration'], $med['instructions'] ?? null
                ]);
            }

            ApiResponse::success([
                'id' => $prescriptionId,
                'prescription_number' => $prescriptionNumber
            ], "Prescription created");
        } elseif ($method === 'GET') {
            $page = max(1, (int)($input['page'] ?? 1));
            $perPage = 20;
            $offset = ($page - 1) * $perPage;

            $stmt = $pdo->prepare("SELECT COUNT(*) as c FROM prescriptions WHERE dentist_id = ?");
            $stmt->execute([$dentistId]);
            $total = $stmt->fetch()['c'];

            $stmt = $pdo->prepare("SELECT pr.*, u_p.name as patient_name FROM prescriptions pr JOIN patients pt ON pr.patient_id = pt.id JOIN users u_p ON pt.user_id = u_p.id WHERE pr.dentist_id = ? ORDER BY pr.created_at DESC LIMIT $perPage OFFSET $offset");
            $stmt->execute([$dentistId]);
            $prescriptions = $stmt->fetchAll();

            ApiResponse::paginated($prescriptions, $total, $page, $perPage);
        }
        break;

    // =====================================================
    // TREATMENT PLANS
    // =====================================================
    case 'treatment-plans':
        if ($method === 'POST') {
            $required = ['patient_id', 'title'];
            foreach ($required as $field) {
                if (empty($input[$field])) ApiResponse::error("Missing: $field");
            }

            $stmt = $pdo->prepare("INSERT INTO treatment_plans (patient_id, dentist_id, title, description, total_cost, estimated_sessions, start_date, end_date, status, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $input['patient_id'], $dentistId, $input['title'],
                $input['description'] ?? null, $input['total_cost'] ?? 0,
                $input['estimated_sessions'] ?? 1, $input['start_date'] ?? null,
                $input['end_date'] ?? null, $input['status'] ?? 'proposed',
                $input['notes'] ?? null
            ]);
            $planId = $pdo->lastInsertId();

            if (!empty($input['items'])) {
                $itemStmt = $pdo->prepare("INSERT INTO treatment_plan_items (plan_id, treatment_id, step_number, title, description, cost) VALUES (?, ?, ?, ?, ?, ?)");
                foreach ($input['items'] as $idx => $item) {
                    $itemStmt->execute([
                        $planId, $item['treatment_id'] ?? null, $idx + 1,
                        $item['title'], $item['description'] ?? null, $item['cost'] ?? 0
                    ]);
                }
            }

            ApiResponse::success(['id' => $planId], "Treatment plan created");
        } elseif ($method === 'GET') {
            $stmt = $pdo->prepare("SELECT tp.*, u_p.name as patient_name FROM treatment_plans tp JOIN patients pt ON tp.patient_id = pt.id JOIN users u_p ON pt.user_id = u_p.id WHERE tp.dentist_id = ? ORDER BY tp.created_at DESC");
            $stmt->execute([$dentistId]);
            $plans = $stmt->fetchAll();

            ApiResponse::success($plans);
        }
        break;

    // =====================================================
    // SCHEDULE MANAGEMENT
    // =====================================================
    case 'schedule':
        if ($method === 'POST') {
            // Add/update schedule
            $stmt = $pdo->prepare("INSERT INTO dentist_schedules (dentist_id, schedule_date, start_time, end_time, is_available, reason) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE is_available = VALUES(is_available), end_time = VALUES(end_time), reason = VALUES(reason)");
            $stmt->execute([
                $dentistId, $input['date'], $input['start_time'],
                $input['end_time'], $input['is_available'] ?? 'yes',
                $input['reason'] ?? null
            ]);
            ApiResponse::success(null, "Schedule updated");
        } elseif ($method === 'GET') {
            $startDate = $input['start_date'] ?? date('Y-m-d');
            $endDate = $input['end_date'] ?? date('Y-m-d', strtotime('+30 days'));

            $stmt = $pdo->prepare("SELECT * FROM dentist_schedules WHERE dentist_id = ? AND schedule_date BETWEEN ? AND ? ORDER BY schedule_date, start_time");
            $stmt->execute([$dentistId, $startDate, $endDate]);
            $schedules = $stmt->fetchAll();

            // Also get booked appointments
            $stmt = $pdo->prepare("SELECT a.appointment_date, a.start_time, a.end_time, a.status, u_patient.name as patient_name FROM appointments a JOIN patients p ON a.patient_id = p.id JOIN users u_patient ON p.user_id = u_patient.id WHERE a.dentist_id = ? AND a.appointment_date BETWEEN ? AND ? AND a.status IN ('confirmed', 'pending') ORDER BY a.appointment_date, a.start_time");
            $stmt->execute([$dentistId, $startDate, $endDate]);
            $booked = $stmt->fetchAll();

            ApiResponse::success([
                'schedules' => $schedules,
                'booked_appointments' => $booked
            ]);
        } elseif ($method === 'DELETE') {
            $scheduleId = $input['schedule_id'] ?? null;
            if ($scheduleId) {
                $stmt = $pdo->prepare("DELETE FROM dentist_schedules WHERE id = ? AND dentist_id = ?");
                $stmt->execute([$scheduleId, $dentistId]);
            }
            ApiResponse::success(null, "Schedule removed");
        }
        break;

    // =====================================================
    // NOTIFICATIONS
    // =====================================================
    case 'notifications':
        $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 50");
        $stmt->execute([$user['user_id']]);
        $notifications = $stmt->fetchAll();
        ApiResponse::success($notifications);
        break;

    // =====================================================
    // PROFILE
    // =====================================================
    case 'profile':
        if ($method === 'GET') {
            $stmt = $pdo->prepare("SELECT u.name, u.email, u.phone, u.profile_image, d.* FROM users u JOIN dentists d ON u.id = d.user_id WHERE d.id = ?");
            $stmt->execute([$dentistId]);
            ApiResponse::success($stmt->fetch());
        } elseif ($method === 'PUT') {
            $allowed = ['name', 'phone', 'profile_image', 'specialization', 'qualification', 'bio', 'consultation_fee'];
            $userUpdates = [];
            $dentistUpdates = [];
            $userParams = [];
            $dentistParams = [];

            foreach ($allowed as $field) {
                if (isset($input[$field])) {
                    if (in_array($field, ['name', 'phone', 'profile_image'])) {
                        $userUpdates[] = "$field = ?";
                        $userParams[] = $input[$field];
                    } else {
                        $dentistUpdates[] = "$field = ?";
                        $dentistParams[] = $input[$field];
                    }
                }
            }

            if (!empty($userUpdates)) {
                $userParams[] = $user['user_id'];
                $stmt = $pdo->prepare("UPDATE users SET " . implode(', ', $userUpdates) . " WHERE id = ?");
                $stmt->execute($userParams);
            }
            if (!empty($dentistUpdates)) {
                $dentistParams[] = $dentistId;
                $stmt = $pdo->prepare("UPDATE dentists SET " . implode(', ', $dentistUpdates) . " WHERE id = ?");
                $stmt->execute($dentistParams);
            }

            ApiResponse::success(null, "Profile updated");
        }
        break;

    default:
        ApiResponse::error("Invalid dentist endpoint: $action", 404);
}
