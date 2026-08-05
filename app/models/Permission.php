<?php
class Permission extends Model {
    protected $table = 'permissions';
    protected $fillable = ['name', 'description', 'module', 'action', 'status'];
    protected $searchable = ['name', 'description', 'module', 'action'];
    protected $filterable = ['module', 'action', 'status'];

    public function getGroupedByModule() {
        $sql = "SELECT module, action, name, description FROM permissions WHERE status = 'active' ORDER BY module, action";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $permissions = $stmt->fetchAll();

        $grouped = [];
        foreach ($permissions as $permission) {
            $module = $permission['module'];
            $action = $permission['action'];

            if (!isset($grouped[$module])) {
                $grouped[$module] = [];
            }

            $grouped[$module][] = [
                'name' => $permission['name'],
                'description' => $permission['description'],
                'action' => $action
            ];
        }

        return $grouped;
    }
}
