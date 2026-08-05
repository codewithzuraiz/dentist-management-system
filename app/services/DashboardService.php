<?php
class DashboardService {
    private $db;
    private $dentistService;
    private $patientService;
    private $appointmentService;
    private $invoiceService;
    private $treatmentService;

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
        $this->dentistService = new DentistService($database);
        $this->patientService = new PatientService($database);
        $this->appointmentService = new AppointmentService($database);
        $this->invoiceService = new InvoiceService($database);
        $this->treatmentService = new TreatmentService($database);
    }

    public function getDashboardStats() {
        return [
            'total_dentists' => $this->dentistService->getTotalCount(),
            'total_patients' => $this->patientService->getTotalCount(),
            'today_appointments' => $this->appointmentService->getTodayAppointmentsCount(),
            'pending_appointments' => $this->appointmentService->getPendingAppointmentsCount(),
            'completed_treatments' => $this->getCompletedTreatmentsCount(),
            'total_revenue' => $this->invoiceService->getTotalRevenue(),
            'pending_payments' => $this->invoiceService->getPendingPayments(),
            'new_patients_this_month' => $this->patientService->getNewPatientsThisMonth()
        ];
    }

    public function getMonthlyRevenueData($period = '365') {
        $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total_amount) as revenue FROM invoices WHERE status IN ('paid', 'partially_paid') AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY) GROUP BY DATE_FORMAT(created_at, '%Y-%m') ORDER BY created_at";
        $stmt = $this->db->prepare($sql);
        $days = $period === '7' ? 7 : ($period === '30' ? 30 : ($period === '90' ? 90 : 365));
        $stmt->execute([$days]);
        return $stmt->fetchAll();
    }

    public function getAppointmentsOverview() {
        $sql = "SELECT status, COUNT(*) as count FROM appointments WHERE appointment_date = CURDATE() GROUP BY status";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $overview = ['completed' => 0, 'pending' => 0, 'cancelled' => 0, 'no_show' => 0];
        foreach ($result as $row) {
            $overview[$row['status']] = $row['count'];
        }

        return $overview;
    }

    public function getMostCommonTreatments($limit = 6) {
        $sql = "SELECT t.name, COUNT(*) as count FROM invoice_items ii JOIN treatments t ON ii.treatment_id = t.id JOIN invoices i ON ii.invoice_id = i.id WHERE i.status IN ('paid', 'partially_paid') GROUP BY t.name ORDER BY count DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getRecentActivities($limit = 6) {
        $sql = "SELECT a.* FROM audit_logs a LEFT JOIN users u ON a.user_id = u.id WHERE a.action IN ('create', 'update', 'payment') ORDER BY a.created_at DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getUpcomingAppointments($limit = 5) {
        $sql = "SELECT a.*, u_patient.name as patient_name, u_dentist.name as dentist_name, t.name as treatment_name FROM appointments a JOIN patients p ON a.patient_id = p.id JOIN users u_patient ON p.user_id = u_patient.id JOIN dentists d ON a.dentist_id = d.id JOIN users u_dentist ON d.user_id = u_dentist.id JOIN treatments t ON a.treatment_id = t.id WHERE a.status IN ('confirmed','pending') AND a.appointment_date >= CURDATE() ORDER BY a.appointment_date, a.start_time LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getRevenueSummary() {
        $today = date('Y-m-d');
        $sql = "SELECT
            (SELECT SUM(total_amount) FROM invoices WHERE DATE(created_at) = ? AND status IN ('paid', 'partially_paid')) as today_revenue,
            (SELECT SUM(total_amount) FROM invoices WHERE DATE(created_at) >= DATE_SUB(?, INTERVAL 7 DAY) AND status IN ('paid', 'partially_paid')) as week_revenue,
            (SELECT SUM(total_amount) FROM invoices WHERE DATE(created_at) >= DATE_SUB(?, INTERVAL 30 DAY) AND status IN ('paid', 'partially_paid')) as month_revenue,
            (SELECT SUM(total_amount) FROM invoices WHERE DATE(created_at) >= DATE_SUB(?, INTERVAL 365 DAY) AND status IN ('paid', 'partially_paid')) as year_revenue,
            (SELECT SUM(total_amount - paid_amount) FROM invoices WHERE status IN ('unpaid', 'partially_paid', 'overdue')) as pending_amount
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$today, $today, $today, $today]);
        return $stmt->fetch();
    }

    public function getCompletedTreatmentsCount() {
        $sql = "SELECT COUNT(*) as count FROM appointments WHERE status = 'completed' AND DATE(appointment_date) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }
}
