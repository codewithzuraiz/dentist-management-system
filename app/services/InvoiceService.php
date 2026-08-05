<?php
class InvoiceService {
    private $db;

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
    }

    public function getTotalRevenue() {
        $sql = "SELECT COALESCE(SUM(total_amount), 0) as total_revenue FROM invoices WHERE status IN ('paid', 'partially_paid')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total_revenue'];
    }

    public function getPendingPayments() {
        $sql = "SELECT COALESCE(SUM(total_amount - paid_amount), 0) as pending_amount FROM invoices WHERE status IN ('unpaid', 'partially_paid', 'overdue')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['pending_amount'];
    }

    public function getAll($filters = [], $search = null, $sortBy = 'created_at', $sortOrder = 'desc', $limit = null, $offset = null) {
        $sql = "SELECT i.*, p.name as patient_name FROM invoices i JOIN patients p ON i.patient_id = p.id WHERE 1=1";

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
                    $whereConditions[] = "i.$key IN ('" . implode("', '", $value) . "')";
                } else {
                    $whereConditions[] = "i.$key = ?";
                    $params[] = $value;
                }
            }
        }

        if ($search) {
            $whereConditions[] = "(i.invoice_number LIKE ? OR p.name LIKE ?)";
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
        $sql = "SELECT i.*, u_patient.name as patient_name, u_dentist.name as dentist_name FROM invoices i JOIN patients p ON i.patient_id = p.id JOIN users u_patient ON p.user_id = u_patient.id JOIN dentists d ON i.dentist_id = d.id JOIN users u_dentist ON d.user_id = u_dentist.id WHERE i.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $this->db->beginTransaction();

        try {
            $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

            $sql = "INSERT INTO invoices (invoice_number, patient_id, dentist_id, appointment_id, subtotal, tax_rate, discount, paid_amount, payment_date, due_date, status, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $invoiceNumber, $data['patient_id'], $data['dentist_id'], $data['appointment_id'] ?? null,
                $data['subtotal'], $data['tax_rate'] ?? 0, $data['discount'] ?? 0, $data['paid_amount'] ?? 0,
                $data['payment_date'] ?? null, $data['due_date'], $data['status'] ?? 'unpaid', $data['notes'] ?? null
            ]);

            $invoiceId = $this->db->lastInsertId();

            if (!empty($data['items'])) {
                $itemSql = "INSERT INTO invoice_items (invoice_id, treatment_id, quantity, unit_price, discount) VALUES (?, ?, ?, ?, ?)";
                $itemStmt = $this->db->prepare($itemSql);

                foreach ($data['items'] as $item) {
                    $itemStmt->execute([
                        $invoiceId, $item['treatment_id'], $item['quantity'] ?? 1,
                        $item['unit_price'], $item['discount'] ?? 0
                    ]);
                }
            }

            $this->db->commit();
            return $invoiceId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update($id, $data) {
        $this->db->beginTransaction();

        try {
            $sql = "UPDATE invoices SET patient_id = ?, dentist_id = ?, appointment_id = ?, subtotal = ?, tax_rate = ?, discount = ?, paid_amount = ?, payment_date = ?, due_date = ?, status = ?, notes = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['patient_id'], $data['dentist_id'], $data['appointment_id'] ?? null,
                $data['subtotal'], $data['tax_rate'] ?? 0, $data['discount'] ?? 0, $data['paid_amount'] ?? 0,
                $data['payment_date'] ?? null, $data['due_date'], $data['status'] ?? 'unpaid', $data['notes'] ?? null, $id
            ]);

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
            $sql = "DELETE FROM invoice_items WHERE invoice_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);

            $sql = "DELETE FROM invoices WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function updateStatus($id) {
        $sql = "SELECT total_amount, paid_amount, due_date FROM invoices WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $invoice = $stmt->fetch();

        $remaining = $invoice['total_amount'] - $invoice['paid_amount'];
        $status = 'unpaid';

        if ($remaining <= 0) {
            $status = 'paid';
        } elseif ($remaining < $invoice['total_amount']) {
            $status = 'partially_paid';
        } elseif (strtotime($invoice['due_date']) < time()) {
            $status = 'overdue';
        }

        $sql = "UPDATE invoices SET status = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$status, $id]);
    }
}
