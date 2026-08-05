<?php
class Invoice extends Model {
    protected $table = 'invoices';
    protected $fillable = [
        'invoice_number', 'patient_id', 'dentist_id', 'appointment_id',
        'subtotal', 'tax_rate', 'discount', 'paid_amount', 'payment_date',
        'due_date', 'status', 'notes'
    ];
    protected $searchable = ['invoice_number', 'notes'];
    protected $filterable = ['status', 'created_at'];

    public function getPatient() {
        $sql = "SELECT p.* FROM patients p WHERE p.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->patient_id]);
        return $stmt->fetch();
    }

    public function getDentist() {
        $sql = "SELECT d.* FROM dentists d WHERE d.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->dentist_id]);
        return $stmt->fetch();
    }

    public function getTreatment() {
        $sql = "SELECT * FROM invoice_items ii JOIN treatments t ON ii.treatment_id = t.id WHERE ii.invoice_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->id]);
        return $stmt->fetchAll();
    }

    public function getPayments() {
        $sql = "SELECT * FROM payments WHERE invoice_id = ? ORDER BY payment_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->id]);
        return $stmt->fetchAll();
    }

    public function getRemainingAmount() {
        return $this->total_amount - $this->paid_amount;
    }

    public function canBePaid($amount) {
        return ($this->paid_amount + $amount) <= $this->total_amount;
    }

    public function updateStatus() {
        $remaining = $this->getRemainingAmount();
        if ($remaining <= 0) {
            $this->status = 'paid';
        } elseif ($remaining < $this->total_amount) {
            $this->status = 'partially_paid';
        } elseif (strtotime($this->due_date) < time()) {
            $this->status = 'overdue';
        }
        $this->update($this->id, ['status' => $this->status]);
    }
}
