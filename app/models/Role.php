<?php
class Role extends Model {
    protected $table = 'roles';
    protected $fillable = ['name', 'description', 'status'];
    protected $searchable = ['name', 'description'];

    public function getPermissions() {
        $sql = "SELECT p.* FROM permissions p JOIN role_permissions rp ON p.id = rp.permission_id WHERE rp.role_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->id]);
        return $stmt->fetchAll();
    }

    public function syncPermissions($permissionIds) {
        $this->db->beginTransaction();

        try {
            $sql = "DELETE FROM role_permissions WHERE role_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$this->id]);

            if (!empty($permissionIds)) {
                $sql = "INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)";
                $stmt = $this->db->prepare($sql);

                foreach ($permissionIds as $permissionId) {
                    $stmt->execute([$this->id, $permissionId]);
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
