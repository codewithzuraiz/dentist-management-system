<?php
class Dentist extends Model {
    protected $table = 'dentists';
    protected $fillable = [
        'user_id', 'specialization', 'qualification', 'experience_years',
        'consultation_fee', 'bio', 'profile_image', 'status', 'working_days',
        'working_start_time', 'working_end_time', 'break_start_time', 'break_end_time'
    ];
    protected $searchable = ['specialization', 'qualification', 'bio'];
    protected $filterable = ['status', 'specialization', 'experience_years'];

    public function getUser() {
        $sql = "SELECT u.* FROM users u WHERE u.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->user_id]);
        return $stmt->fetch();
    }

    public function getAppointments($filters = [], $limit = 10) {
        $sql = "SELECT a.* FROM appointments a WHERE a.dentist_id = ?";

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

    public function getPatients() {
        $sql = "SELECT p.* FROM patients p JOIN appointments a ON p.id = a.patient_id WHERE a.dentist_id = ? GROUP BY p.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->id]);
        return $stmt->fetchAll();
    }

    public function getEarnings($startDate = null, $endDate = null) {
        $sql = "SELECT SUM(i.total_amount) as total_earnings, COUNT(i.id) as invoice_count FROM invoices i JOIN appointments a ON i.appointment_id = a.id WHERE a.dentist_id = ?";

        $params = [$this->id];

        if ($startDate && $endDate) {
            $sql .= " AND i.created_at BETWEEN ? AND ?";
            $params[] = $startDate . ' 00:00:00';
            $params[] = $endDate . ' 23:59:59';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
}
