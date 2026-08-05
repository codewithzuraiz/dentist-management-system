<?php
class PrescriptionService {
    private $db;

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
    }

    public function getAll($filters = [], $search = null, $sortBy = 'issued_date', $sortOrder = 'desc', $limit = null, $offset = null) {
        $sql = "SELECT p.* FROM prescriptions p JOIN patients pat ON p.patient_id = pat.id JOIN dentists d ON p.dentist_id = d.id WHERE 1=1";

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
            $whereConditions[] = "(p.prescription_number LIKE ? OR pat.name LIKE ? OR d.name LIKE ?)";
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
        $sql = "SELECT p.* FROM prescriptions p JOIN patients pat ON p.patient_id = pat.id JOIN dentists d ON p.dentist_id = d.id WHERE p.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $this->db->beginTransaction();

        try {
            $prescriptionNumber = 'RX-' . date('Y') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

            $sql = "INSERT INTO prescriptions (prescription_number, patient_id, dentist_id, appointment_id, issued_date, expiry_date, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $prescriptionNumber, $data['patient_id'], $data['dentist_id'], $data['appointment_id'] ?? null,
                $data['issued_date'] ?? date('Y-m-d H:i:s'), $data['expiry_date'], $data['notes'] ?? null, $data['status'] ?? 'active'
            ]);

            $prescriptionId = $this->db->lastInsertId();

            if (!empty($data['medicines'])) {
                $medicineSql = "INSERT INTO prescription_medicines (prescription_id, medicine_name, dosage, frequency, duration, instructions) VALUES (?, ?, ?, ?, ?, ?)";
                $medicineStmt = $this->db->prepare($medicineSql);

                foreach ($data['medicines'] as $medicine) {
                    $medicineStmt->execute([
                        $prescriptionId, $medicine['name'], $medicine['dosage'], $medicine['frequency'],
                        $medicine['duration'], $medicine['instructions'] ?? null
                    ]);
                }
            }

            $this->db->commit();
            return $prescriptionId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update($id, $data) {
        $this->db->beginTransaction();

        try {
            $sql = "UPDATE prescriptions SET patient_id = ?, dentist_id = ?, appointment_id = ?, issued_date = ?, expiry_date = ?, notes = ?, status = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['patient_id'], $data['dentist_id'], $data['appointment_id'] ?? null,
                $data['issued_date'] ?? date('Y-m-d H:i:s'), $data['expiry_date'], $data['notes'] ?? null, $data['status'] ?? 'active', $id
            ]);

            if (isset($data['medicines'])) {
                $sql = "DELETE FROM prescription_medicines WHERE prescription_id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$id]);

                $medicineSql = "INSERT INTO prescription_medicines (prescription_id, medicine_name, dosage, frequency, duration, instructions) VALUES (?, ?, ?, ?, ?, ?)";
                $medicineStmt = $this->db->prepare($medicineSql);

                foreach ($data['medicines'] as $medicine) {
                    $medicineStmt->execute([
                        $id, $medicine['name'], $medicine['dosage'], $medicine['frequency'],
                        $medicine['duration'], $medicine['instructions'] ?? null
                    ]);
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function delete($id) {
        $this->db->beginTransaction();

        try {
            $sql = "DELETE FROM prescription_medicines WHERE prescription_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);

            $sql = "DELETE FROM prescriptions WHERE id = ?";
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
