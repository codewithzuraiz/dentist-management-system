<?php
class PaymentService {
    private $db;

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
    }

    public function getAll($filters = [], $search = null, $sortBy = 'payment_date', $sortOrder = 'desc', $limit = null, $offset = null) {
        $sql = "SELECT p.*, i.invoice_number, pat.name as patient_name FROM payments p JOIN invoices i ON p.invoice_id = i.id JOIN patients pat ON p.patient_id = pat.id WHERE 1=1";

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
            $whereConditions[] = "(p.transaction_reference LIKE ? OR pat.name LIKE ?)";
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
        $sql = "SELECT p.*, i.invoice_number, pat.name as patient_name FROM payments p JOIN invoices i ON p.invoice_id = i.id JOIN patients pat ON p.patient_id = pat.id WHERE p.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $this->db->beginTransaction();

        try {
            $sql = "INSERT INTO payments (invoice_id, patient_id, amount, payment_method, transaction_reference, payment_date, recorded_by, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['invoice_id'], $data['patient_id'], $data['amount'],
                $data['payment_method'], $data['transaction_reference'],
                $data['payment_date'] ?? date('Y-m-d H:i:s'), $data['recorded_by'],
                $data['notes'] ?? null
            ]);

            $paymentId = $this->db->lastInsertId();

            $invoiceService = new InvoiceService($this->db);
            $invoice = $invoiceService->find($data['invoice_id']);
            $newPaidAmount = $invoice['paid_amount'] + $data['amount'];

            $invoiceService->update($data['invoice_id'], ['paid_amount' => $newPaidAmount]);
            $invoiceService->updateStatus($data['invoice_id']);

            $this->db->commit();
            return $paymentId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update($id, $data) {
        $this->db->beginTransaction();

        try {
            $sql = "UPDATE payments SET invoice_id = ?, patient_id = ?, amount = ?, payment_method = ?, transaction_reference = ?, payment_date = ?, recorded_by = ?, notes = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['invoice_id'], $data['patient_id'], $data['amount'],
                $data['payment_method'], $data['transaction_reference'],
                $data['payment_date'] ?? date('Y-m-d H:i:s'), $data['recorded_by'],
                $data['notes'] ?? null, $id
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
            $sql = "SELECT invoice_id, amount FROM payments WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $payment = $stmt->fetch();

            $invoiceService = new InvoiceService($this->db);
            $invoice = $invoiceService->find($payment['invoice_id']);
            $newPaidAmount = $invoice['paid_amount'] - $payment['amount'];

            $invoiceService->update($payment['invoice_id'], ['paid_amount' => $newPaidAmount]);
            $invoiceService->updateStatus($payment['invoice_id']);

            $sql = "DELETE FROM payments WHERE id = ?";
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
