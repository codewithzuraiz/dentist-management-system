<?php
class Payment extends Model {
    protected $table = 'payments';
    protected $fillable = [
        'invoice_id', 'patient_id', 'amount', 'payment_method', 'transaction_reference',
        'payment_date', 'recorded_by', 'notes'
    ];
    protected $searchable = ['transaction_reference', 'notes'];
    protected $filterable = ['payment_method', 'payment_date'];

    public function getInvoice() {
        $sql = "SELECT i.* FROM invoices i WHERE i.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->invoice_id]);
        return $stmt->fetch();
    }

    public function getPatient() {
        $sql = "SELECT p.* FROM patients p WHERE p.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->patient_id]);
        return $stmt->fetch();
    }

    public function getRecordedBy() {
        $sql = "SELECT u.* FROM users u WHERE u.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->recorded_by]);
        return $stmt->fetch();
    }
}
