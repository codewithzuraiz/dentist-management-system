<?php
class ReportService {
    private $db;

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
    }

    public function generateRevenueReport($startDate, $endDate) {
        $sql = "SELECT DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as invoices FROM invoices WHERE created_at BETWEEN ? AND ? AND status IN ('paid', 'partially_paid') GROUP BY DATE(created_at) ORDER BY created_at";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        $revenueData = $stmt->fetchAll();

        $totalRevenue = array_sum(array_column($revenueData, 'revenue'));
        $totalInvoices = array_sum(array_column($revenueData, 'invoices'));

        return [
            'revenue' => $revenueData,
            'summary' => [
                'total_revenue' => $totalRevenue,
                'total_invoices' => $totalInvoices,
                'average_daily_revenue' => $totalRevenue / max(count($revenueData), 1)
            ]
        ];
    }

    public function generateAppointmentsReport($startDate, $endDate) {
        $sql = "SELECT DATE(appointment_date) as date, status, COUNT(*) as count FROM appointments WHERE appointment_date BETWEEN ? AND ? GROUP BY DATE(appointment_date), status ORDER BY appointment_date";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        $appointmentData = $stmt->fetchAll();

        $totalAppointments = array_sum(array_column($appointmentData, 'count'));
        $statusSummary = [];
        foreach ($appointmentData as $row) {
            $statusSummary[$row['status']] = ($statusSummary[$row['status']] ?? 0) + $row['count'];
        }

        return [
            'appointments' => $appointmentData,
            'summary' => [
                'total_appointments' => $totalAppointments,
                'status_breakdown' => $statusSummary
            ]
        ];
    }

    public function generatePatientsReport($startDate, $endDate) {
        $sql = "SELECT DATE(registration_date) as date, COUNT(*) as count FROM patients WHERE registration_date BETWEEN ? AND ? GROUP BY DATE(registration_date) ORDER BY registration_date";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        $patientData = $stmt->fetchAll();

        $totalPatients = array_sum(array_column($patientData, 'count'));

        return [
            'patients' => $patientData,
            'summary' => [
                'total_patients' => $totalPatients
            ]
        ];
    }

    public function generateTreatmentsReport($startDate, $endDate) {
        $sql = "SELECT t.name, COUNT(*) as count, SUM(ii.subtotal) as revenue FROM invoice_items ii JOIN treatments t ON ii.treatment_id = t.id JOIN invoices i ON ii.invoice_id = i.id WHERE i.created_at BETWEEN ? AND ? AND i.status IN ('paid', 'partially_paid') GROUP BY t.name ORDER BY count DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        $treatmentData = $stmt->fetchAll();

        $totalTreatments = array_sum(array_column($treatmentData, 'count'));
        $totalRevenue = array_sum(array_column($treatmentData, 'revenue'));

        return [
            'treatments' => $treatmentData,
            'summary' => [
                'total_treatments' => $totalTreatments,
                'total_revenue' => $totalRevenue
            ]
        ];
    }

    public function generateDentistPerformanceReport($startDate, $endDate) {
        $sql = "SELECT d.id, d.name, d.specialization, COUNT(a.id) as appointments, COUNT(CASE WHEN a.status = 'completed' THEN 1 END) as completed, SUM(i.total_amount) as revenue FROM dentists d LEFT JOIN appointments a ON d.id = a.dentist_id LEFT JOIN invoices i ON a.id = i.appointment_id WHERE a.appointment_date BETWEEN ? AND ? OR a.appointment_date IS NULL GROUP BY d.id ORDER BY revenue DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        $dentistData = $stmt->fetchAll();

        return [
            'dentistPerformance' => $dentistData
        ];
    }

    public function generatePaymentSummaryReport($startDate, $endDate) {
        $sql = "SELECT payment_method, COUNT(*) as count, SUM(amount) as total FROM payments WHERE payment_date BETWEEN ? AND ? GROUP BY payment_method ORDER BY total DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        $paymentData = $stmt->fetchAll();

        $totalPayments = array_sum(array_column($paymentData, 'count'));
        $totalAmount = array_sum(array_column($paymentData, 'total'));

        return [
            'paymentSummary' => $paymentData,
            'summary' => [
                'total_payments' => $totalPayments,
                'total_amount' => $totalAmount
            ]
        ];
    }
}
