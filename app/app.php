<?php
$basePath = dirname(__DIR__);

require_once $basePath . '/app/Database.php';
require_once $basePath . '/app/services/DashboardService.php';
require_once $basePath . '/app/services/DentistService.php';
require_once $basePath . '/app/services/PatientService.php';
require_once $basePath . '/app/services/AppointmentService.php';
require_once $basePath . '/app/services/InvoiceService.php';
require_once $basePath . '/app/services/TreatmentService.php';
require_once $basePath . '/app/services/PaymentService.php';

$database = new Database();
$dashboardService = new DashboardService($database);
$dentistService = new DentistService($database);
$patientService = new PatientService($database);
$appointmentService = new AppointmentService($database);
$invoiceService = new InvoiceService($database);
$treatmentService = new TreatmentService($database);
$paymentService = new PaymentService($database);
