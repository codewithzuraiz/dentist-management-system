<?php
/*
 * Application Services Initialization
 * Lazy-loads database connection and services only when needed
 */

$basePath = dirname(__DIR__);

require_once $basePath . '/app/Database.php';

function getDatabase() {
    return new Database();
}

function getDashboardService() {
    return new DashboardService(getDatabase());
}

function getDentistService() {
    return new DentistService(getDatabase());
}

function getPatientService() {
    return new PatientService(getDatabase());
}

function getAppointmentService() {
    return new AppointmentService(getDatabase());
}

function getInvoiceService() {
    return new InvoiceService(getDatabase());
}

function getTreatmentService() {
    return new TreatmentService(getDatabase());
}

function getPaymentService() {
    return new PaymentService(getDatabase());
}
