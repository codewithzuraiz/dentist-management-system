<?php
class MedicalRecordService {
    private $db;

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
    }

    public function getAll($filters = [], $search = null, $sortBy = 'created_at', $sortOrder = 'desc', $limit = null, $offset = null) {
        $sql = "SELECT m.* FROM medical_records m JOIN patients pat ON m.patient_id = pat.id JOIN dentists d ON m.dentist_id = d.id WHERE 1=1";

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
                    $whereConditions[] = "m.$key IN ('" . implode("', '", $value) . "')";
                } else {
                    $whereConditions[] = "m.$key = ?";
                    $params[] = $value;
                }
            }
        }

        if ($search) {
            $whereConditions[] = "(m.title LIKE ? OR m.description LIKE ? OR pat.name LIKE ?)";
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
        $sql = "SELECT m.* FROM medical_records m JOIN patients pat ON m.patient_id = pat.id JOIN dentists d ON m.dentist_id = d.id WHERE m.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $this->db->beginTransaction();

        try {
            $sql = "INSERT INTO medical_records (patient_id, dentist_id, appointment_id, record_type, title, description, file_path, file_type, file_size, uploaded_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['patient_id'], $data['dentist_id'], $data['appointment_id'] ?? null,
                $data['record_type'], $data['title'], $data['description'],
                $data['file_path'], $data['file_type'], $data['file_size'],
                $data['uploaded_by']
            ]);

            $medicalRecordId = $this->db->lastInsertId();

            $this->db->commit();
            return $medicalRecordId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update($id, $data) {
        $sql = "UPDATE medical_records SET patient_id = ?, dentist_id = ?, appointment_id = ?, record_type = ?, title = ?, description = ?, file_path = ?, file_type = ?, file_size = ?, uploaded_by = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['patient_id'], $data['dentist_id'], $data['appointment_id'] ?? null,
            $data['record_type'], $data['title'], $data['description'],
            $data['file_path'], $data['file_type'], $data['file_size'],
            $data['uploaded_by'], $id
        ]);

        return $stmt->rowCount() > 0;
    }

    public function delete($id) {
        $sql = "DELETE FROM medical_records WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
