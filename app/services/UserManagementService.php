<?php
class UserManagementService {
    private $db;

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
    }

    public function getAllUsers($filters = [], $search = null, $sortBy = 'name', $sortOrder = 'asc', $limit = null, $offset = null) {
        $sql = "SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE 1=1";

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
                    $whereConditions[] = "u.$key IN ('" . implode("', '", $value) . "')";
                } else {
                    $whereConditions[] = "u.$key = ?";
                    $params[] = $value;
                }
            }
        }

        if ($search) {
            $whereConditions[] = "(u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)";
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

    public function findUser($id) {
        $sql = "SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function createUser($data) {
        $this->db->beginTransaction();

        try {
            $sql = "INSERT INTO users (role_id, name, email, phone, password, profile_image, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['role_id'], $data['name'], $data['email'], $data['phone'],
                $data['password'], $data['profile_image'] ?? null, $data['status'] ?? 'active'
            ]);

            $userId = $this->db->lastInsertId();

            $this->db->commit();
            return $userId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function updateUser($id, $data) {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        $sql = "UPDATE users SET " . implode(' = ?, ', $columns) . " = ? WHERE id = ?";
        $params = array_values($data);
        $params[] = $id;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function deleteUser($id) {
        $this->db->beginTransaction();

        try {
            $sql = "DELETE FROM users WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getRoles() {
        $sql = "SELECT * FROM roles WHERE status = 'active' ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getPermissions() {
        $sql = "SELECT * FROM permissions WHERE status = 'active' ORDER BY module, action";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUserPermissions($roleId) {
        $sql = "SELECT p.* FROM permissions p JOIN role_permissions rp ON p.id = rp.permission_id WHERE rp.role_id = ? AND p.status = 'active' ORDER BY p.module, p.action";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$roleId]);
        return $stmt->fetchAll();
    }

    public function updateUserPermissions($roleId, $permissionIds) {
        $this->db->beginTransaction();

        try {
            $sql = "DELETE FROM role_permissions WHERE role_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$roleId]);

            if (!empty($permissionIds)) {
                $sql = "INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)";
                $stmt = $this->db->prepare($sql);

                foreach ($permissionIds as $permissionId) {
                    $stmt->execute([$roleId, $permissionId]);
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
