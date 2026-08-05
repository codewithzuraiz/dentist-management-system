<?php
class PatientService {
    private $db;

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
    }

    public function getTotalCount() {
        $sql = "SELECT COUNT(*) as count FROM patients WHERE status = 'active'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }

    public function getNewPatientsThisMonth() {
        $sql = "SELECT COUNT(*) as count FROM patients WHERE status = 'active' AND MONTH(registration_date) = MONTH(CURRENT_DATE()) AND YEAR(registration_date) = YEAR(CURRENT_DATE())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }

    public function getAll($filters = [], $search = null, $sortBy = 'name', $sortOrder = 'asc', $limit = null, $offset = null) {
        $sql = "SELECT p.*, u.name as user_name, u.email, u.phone FROM patients p JOIN users u ON p.user_id = u.id WHERE 1=1";

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
                    $whereConditions[] = "p.$key IN ('" . implode("', '", $value) . "')";
                } else {
                    $whereConditions[] = "p.$key = ?";
                    $params[] = $value;
                }
            }
        }

        if ($search) {
            $whereConditions[] = "(u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ? OR p.address LIKE ?)";
            $params[] = "%$search%";
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
        $sql = "SELECT p.*, u.name as user_name, u.email, u.phone FROM patients p JOIN users u ON p.user_id = u.id WHERE p.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $this->db->beginTransaction();

        try {
            $sql = "INSERT INTO patients (user_id, gender, date_of_birth, blood_group, address, emergency_contact_name, emergency_contact_phone, allergies, medical_conditions, profile_image, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['user_id'], $data['gender'] ?? 'male', $data['date_of_birth'] ?? null,
                $data['blood_group'], $data['address'], $data['emergency_contact_name'],
                $data['emergency_contact_phone'], $data['allergies'], $data['medical_conditions'],
                $data['profile_image'], $data['status'] ?? 'active'
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
        $sql = "UPDATE patients SET " . implode(' = ?, ', $columns) . " = ? WHERE id = ?";
        $params = array_values($data);
        $params[] = $id;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id) {
        $this->db->beginTransaction();

        try {
            $sql = "DELETE FROM patients WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
