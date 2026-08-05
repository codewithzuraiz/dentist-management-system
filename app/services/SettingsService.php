<?php
class SettingsService {
    private $db;

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
    }

    public function getSettings() {
        $sql = "SELECT * FROM system_settings WHERE id = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function updateSettings($data) {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        $sql = "UPDATE system_settings SET " . implode(' = ?, ', $columns) . " = ? WHERE id = 1";
        $params = array_values($data);
        $params[] = 1;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
}
