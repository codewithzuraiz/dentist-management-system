<?php
class AuditLog extends Model {
    protected $table = 'audit_logs';
    protected $fillable = [
        'user_id', 'action', 'module', 'entity_type', 'entity_id',
        'description', 'old_values', 'new_values', 'ip_address', 'user_agent'
    ];

    public function getUser() {
        $sql = "SELECT u.* FROM users u WHERE u.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->user_id]);
        return $stmt->fetch();
    }

    public function log($data) {
        $this->create($data);
    }
}
