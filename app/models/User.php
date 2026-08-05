<?php
class User extends Model {
    protected $table = 'users';
    protected $fillable = ['role_id', 'name', 'email', 'phone', 'password', 'profile_image', 'status'];
    protected $searchable = ['name', 'email', 'phone'];
    protected $filterable = ['role_id', 'status'];

    public function getRole() {
        $sql = "SELECT r.* FROM roles r JOIN role_permissions rp ON r.id = rp.role_id WHERE rp.permission_id IN (SELECT id FROM permissions WHERE module = 'users') AND r.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->id]);
        return $stmt->fetch();
    }

    public function hasPermission($module, $action) {
        $sql = "SELECT p.* FROM role_permissions rp JOIN permissions p ON rp.permission_id = p.id JOIN roles r ON rp.role_id = r.id WHERE r.id = ? AND p.module = ? AND p.action = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->role_id, $module, $action]);
        return $stmt->fetch() !== false;
    }

    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
}
