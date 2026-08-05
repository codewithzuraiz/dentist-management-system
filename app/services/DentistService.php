<?php
class DentistService {
    private $db;

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
    }

    public function getTotalCount() {
        $sql = "SELECT COUNT(*) as count FROM dentists WHERE status = 'active'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }

    public function getAll($filters = [], $search = null, $sortBy = 'name', $sortOrder = 'asc', $limit = null, $offset = null) {
        $sql = "SELECT d.*, u.name as user_name, u.email, u.phone FROM dentists d JOIN users u ON d.user_id = u.id WHERE 1=1";

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
                    $whereConditions[] = "d.$key IN ('" . implode("', '", $value) . "')";
                } else {
                    $whereConditions[] = "d.$key = ?";
                    $params[] = $value;
                }
            }
        }

        if ($search) {
            $whereConditions[] = "(d.specialization LIKE ? OR d.qualification LIKE ? OR u.name LIKE ?)";
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
        $sql = "SELECT d.*, u.name as user_name, u.email, u.phone FROM dentists d JOIN users u ON d.user_id = u.id WHERE d.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $this->db->beginTransaction();

        try {
            $sql = "INSERT INTO dentists (user_id, specialization, qualification, experience_years, consultation_fee, bio, status, working_days, working_start_time, working_end_time, break_start_time, break_end_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['user_id'], $data['specialization'], $data['qualification'], $data['experience_years'] ?? 0,
                $data['consultation_fee'] ?? 0, $data['bio'], $data['status'] ?? 'active',
                $data['working_days'], $data['working_start_time'], $data['working_end_time'],
                $data['break_start_time'], $data['break_end_time']
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
        $sql = "UPDATE dentists SET " . implode(' = ?, ', $columns) . " = ? WHERE id = ?";
        $params = array_values($data);
        $params[] = $id;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id) {
        $this->db->beginTransaction();

        try {
            $sql = "DELETE FROM dentists WHERE id = ?";
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
