<?php
class NotificationService {
    private $db;

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
    }

    public function getAll($filters = [], $search = null, $sortBy = 'created_at', $sortOrder = 'desc', $limit = null, $offset = null) {
        $sql = "SELECT n.* FROM notifications n JOIN users u ON n.user_id = u.id WHERE 1=1";

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
                    $whereConditions[] = "n.$key IN ('" . implode("', '", $value) . "')";
                } else {
                    $whereConditions[] = "n.$key = ?";
                    $params[] = $value;
                }
            }
        }

        if ($search) {
            $whereConditions[] = "(n.title LIKE ? OR n.message LIKE ? OR u.name LIKE ?)";
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
        $sql = "SELECT n.* FROM notifications n JOIN users u ON n.user_id = u.id WHERE n.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $sql = "INSERT INTO notifications (user_id, type, title, message, related_entity_type, related_entity_id, is_read, sent, sent_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['user_id'], $data['type'], $data['title'], $data['message'],
            $data['related_entity_type'], $data['related_entity_id'], $data['is_read'] ?? 'no',
            $data['sent'] ?? 'no', $data['sent_at'] ?? null
        ]);

        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        $sql = "UPDATE notifications SET " . implode(' = ?, ', $columns) . " = ? WHERE id = ?";
        $params = array_values($data);
        $params[] = $id;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function markAsRead($id) {
        $sql = "UPDATE notifications SET is_read = 'yes' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function send($id) {
        $sql = "UPDATE notifications SET sent = 'yes', sent_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function delete($id) {
        $sql = "DELETE FROM notifications WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
