<?php
class AppointmentService {
    private $db;

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
    }

    public function getTotalCount() {
        $sql = "SELECT COUNT(*) as count FROM appointments WHERE status != 'cancelled'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }

    public function getTodayAppointmentsCount() {
        $sql = "SELECT COUNT(*) as count FROM appointments WHERE DATE(appointment_date) = CURDATE() AND status != 'cancelled'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }

    public function getPendingAppointmentsCount() {
        $sql = "SELECT COUNT(*) as count FROM appointments WHERE status = 'pending'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }

    public function getAll($filters = [], $search = null, $sortBy = 'appointment_date', $sortOrder = 'desc', $limit = null, $offset = null) {
        $sql = "SELECT a.*, u_patient.name as patient_name, u_dentist.name as dentist_name, t.name as treatment_name FROM appointments a JOIN patients p ON a.patient_id = p.id JOIN users u_patient ON p.user_id = u_patient.id JOIN dentists d ON a.dentist_id = d.id JOIN users u_dentist ON d.user_id = u_dentist.id JOIN treatments t ON a.treatment_id = t.id WHERE 1=1";

        $whereConditions = [];
        $params = [];

        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                if (is_array($value)) {
                    $placeholders = [];
                    foreach ($value as $item) {
                        $placeholders[] = '?';
                        $params[] = $item;
                    }
                    if ($key === 'status') {
                        $whereConditions[] = "a.$key IN ('" . implode("', '", $value) . "')";
                    } else {
                        $whereConditions[] = "a.$key IN ('" . implode("', '", $value) . "')";
                    }
                } else {
                    $whereConditions[] = "a.$key = ?";
                    $params[] = $value;
                }
            }
        }

        if ($search) {
            $whereConditions[] = "(p.name LIKE ? OR d.name LIKE ? OR t.name LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        if (!empty($whereConditions)) {
            $sql .= " AND " . implode(' AND ', $whereConditions);
        }

        $sql .= " ORDER BY $sortBy $sortOrder";

        if ($limit) {
            $sql .= " LIMIT ?";
            $params[] = $limit;
        }

        if ($offset) {
            $sql .= " OFFSET ?";
            $params[] = $offset;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function find($id) {
        $sql = "SELECT a.*, u_patient.name as patient_name, u_dentist.name as dentist_name, t.name as treatment_name FROM appointments a JOIN patients p ON a.patient_id = p.id JOIN users u_patient ON p.user_id = u_patient.id JOIN dentists d ON a.dentist_id = d.id JOIN users u_dentist ON d.user_id = u_dentist.id JOIN treatments t ON a.treatment_id = t.id WHERE a.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $this->db->beginTransaction();

        try {
            $sql = "INSERT INTO appointments (patient_id, dentist_id, treatment_id, appointment_date, start_time, end_time, status, notes, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['patient_id'], $data['dentist_id'], $data['treatment_id'],
                $data['appointment_date'], $data['start_time'], $data['end_time'],
                $data['status'] ?? 'pending', $data['notes'], $data['created_by']
            ]);

            $this->db->commit();
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update($id, $data) {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        $sql = "UPDATE appointments SET " . implode(' = ?, ', $columns) . " = ? WHERE id = ?";
        $params = array_values($data);
        $params[] = $id;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id) {
        $this->db->beginTransaction();

        try {
            $sql = "DELETE FROM appointments WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getDentistWorkingHours($dentistId) {
        $sql = "SELECT working_start_time, working_end_time, break_start_time, break_end_time, working_days FROM dentists WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$dentistId]);
        return $stmt->fetch();
    }

    public function checkAvailability($dentistId, $date, $startTime, $endTime) {
        $sql = "SELECT COUNT(*) as conflict_count FROM appointments WHERE dentist_id = ? AND appointment_date = ? AND status IN ('pending', 'confirmed') AND (
            (start_time <= ? AND end_time >= ?) OR
            (start_time <= ? AND end_time >= ?) OR
            (start_time <= ? AND end_time >= ?)
        )";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $dentistId, $date,
            $endTime, $startTime,
            $startTime, $endTime,
            $startTime, $endTime
        ]);
        $result = $stmt->fetch();
        return $result['conflict_count'] == 0;
    }
}
