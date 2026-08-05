<?php
class Patient extends Model {
    protected $table = 'patients';
    protected $fillable = [
        'user_id', 'gender', 'date_of_birth', 'blood_group', 'address',
        'emergency_contact_name', 'emergency_contact_phone', 'allergies',
        'medical_conditions', 'profile_image', 'status'
    ];
    protected $searchable = ['address', 'emergency_contact_name', 'emergency_contact_phone'];
    protected $filterable = ['status', 'gender', 'blood_group'];

    public function getUser() {
        $sql = "SELECT u.* FROM users u WHERE u.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->user_id]);
        return $stmt->fetch();
    }

    public function getAppointments($filters = [], $limit = 10) {
        $sql = "SELECT a.* FROM appointments a WHERE a.patient_id = ?";

        $whereConditions = [];
        $params = [$this->id];

        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                $whereConditions[] = "a.$key = ?";
                $params[] = $value;
            }
        }

        $sql .= !empty($whereConditions) ? " AND " . implode(' AND ', $whereConditions) : "";
        $sql .= " ORDER BY a.appointment_date DESC, a.start_time DESC LIMIT ?";
        $params[] = $limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getInvoices($filters = [], $limit = 10) {
        $sql = "SELECT i.* FROM invoices i WHERE i.patient_id = ?";

        $whereConditions = [];
        $params = [$this->id];

        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                $whereConditions[] = "i.$key = ?";
                $params[] = $value;
            }
        }

        $sql .= !empty($whereConditions) ? " AND " . implode(' AND ', $whereConditions) : "";
        $sql .= " ORDER BY i.created_at DESC LIMIT ?";
        $params[] = $limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getPayments() {
        $sql = "SELECT p.* FROM payments p JOIN invoices i ON p.invoice_id = i.id WHERE i.patient_id = ? ORDER BY p.payment_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->id]);
        return $stmt->fetchAll();
    }

    public function getTotalBalance() {
        $sql = "SELECT SUM(i.total_amount - i.paid_amount) as balance FROM invoices i WHERE i.patient_id = ? AND i.status IN ('unpaid', 'partially_paid')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->id]);
        $result = $stmt->fetch();
        return $result['balance'] ?? 0;
    }

    public function getTimeline() {
        $timeline = [];

        $appointments = $this->getAppointments([], 10);
        foreach ($appointments as $appointment) {
            $timeline[] = [
                'type' => 'appointment',
                'date' => $appointment['appointment_date'],
                'title' => 'Appointment',
                'description' => 'Appointment with Dr. ' . $appointment['dentist_name'],
                'status' => $appointment['status']
            ];
        }

        $invoices = $this->getInvoices([], 10);
        foreach ($invoices as $invoice) {
            $timeline[] = [
                'type' => 'invoice',
                'date' => $invoice['created_at'],
                'title' => 'Invoice',
                'description' => 'Invoice #' . $invoice['invoice_number'],
                'amount' => $invoice['total_amount'],
                'status' => $invoice['status']
            ];
        }

        $payments = $this->getPayments();
        foreach ($payments as $payment) {
            $timeline[] = [
                'type' => 'payment',
                'date' => $payment['payment_date'],
                'title' => 'Payment',
                'description' => 'Payment of ' . $payment['amount'],
                'amount' => $payment['amount'],
                'method' => $payment['payment_method']
            ];
        }

        usort($timeline, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return $timeline;
    }
}
