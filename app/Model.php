<?php
class Model {
    protected $db;
    protected $table;
    protected $fillable = [];
    protected $searchable = [];
    protected $filterable = [];

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
        $this->table = $this->getTableName();
    }

    protected function getTableName() {
        $className = explode('\\', get_class($this));
        return strtolower(end($className));
    }

    public function all($columns = ['*'], $filters = [], $search = null, $sortBy = 'id', $sortOrder = 'desc', $limit = null, $offset = null) {
        $sql = "SELECT " . implode(', ', $columns) . " FROM " . $this->table;

        $whereConditions = [];
        $params = [];

        // Apply filters
        foreach ($filters as $key => $value) {
            if (is_array($value)) {
                $placeholders = [];
                foreach ($value as $item) {
                    $placeholders[] = '?';
                    $params[] = $item;
                }
                $whereConditions[] = $key . ' IN (' . implode(', ', $placeholders) . ')';
            } else {
                $whereConditions[] = $key . ' = ?';
                $params[] = $value;
            }
        }

        // Apply search
        if ($search && !empty($this->searchable)) {
            $searchConditions = [];
            foreach ($this->searchable as $field) {
                $searchConditions[] = "$field LIKE ?";
                $params[] = "%$search%";
            }
            if (!empty($searchConditions)) {
                $whereConditions[] = '(' . implode(' OR ', $searchConditions) . ')';
            }
        }

        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(' AND ', $whereConditions);
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
        $sql = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch();
    }

    public function create($data) {
        $columns = array_keys($data);
        $values = array_values($data);

        $sql = "INSERT INTO " . $this->table . " (`" . implode('`, `', $columns) . "`) VALUES (" . str_repeat('?, ', count($columns)) . '?);";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($values);

        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        $sql = "UPDATE " . $this->table . " SET " . implode(' = ?, ', $columns) . " = ? WHERE id = ?";
        $params = array_values($data);
        $params[] = $id;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function where($conditions, $params = []) {
        $sql = "SELECT * FROM " . $this->table . " WHERE 1=1";
        $whereConditions = [];

        foreach ($conditions as $key => $value) {
            if (is_array($value)) {
                $placeholders = [];
                foreach ($value as $item) {
                    $placeholders[] = '?';
                }
                $whereConditions[] = "$key IN (" . implode(', ', $placeholders) . ')';
                $params = array_merge($params, $value);
            } else {
                $whereConditions[] = "$key = ?";
                $params[] = $value;
            }
        }

        if (!empty($whereConditions)) {
            $sql .= " AND " . implode(' AND ', $whereConditions);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
