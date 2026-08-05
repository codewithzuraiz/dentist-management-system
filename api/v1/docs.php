<?php
/**
 * API Documentation
 * Grin Dental Clinic Mobile App API v1
 */

$basePath = dirname($_SERVER['SCRIPT_NAME']);
$baseUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $basePath;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation - Grin Dental Clinic</title>
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #06a3da;
            --dark: #002345;
            --accent: #1DBFCC;
            --green: #14e34a;
            --light-bg: #f8f9fa;
            --border: #dee2e6;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--light-bg); color: #333; line-height: 1.6; }
        .sidebar {
            position: fixed; left: 0; top: 0; bottom: 0; width: 280px;
            background: var(--dark); color: #fff; overflow-y: auto; z-index: 100;
        }
        .sidebar-header { padding: 24px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-header h2 { font-size: 18px; color: var(--accent); }
        .sidebar-header p { font-size: 12px; color: rgba(255,255,255,0.5); margin-top: 4px; }
        .nav-section { padding: 16px 0 8px; }
        .nav-section h3 { padding: 0 20px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.4); }
        .nav-item { display: block; padding: 8px 20px; color: rgba(255,255,255,0.7); text-decoration: none; font-size: 13px; border-left: 3px solid transparent; transition: all 0.2s; }
        .nav-item:hover, .nav-item.active { background: rgba(255,255,255,0.05); color: #fff; border-left-color: var(--primary); }
        .method { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; min-width: 52px; text-align: center; }
        .method.get { background: #d4edda; color: #155724; }
        .method.post { background: #cce5ff; color: #004085; }
        .method.put { background: #fff3cd; color: #856404; }
        .method.delete { background: #f8d7da; color: #721c24; }
        .main { margin-left: 280px; padding: 32px 40px; }
        .main h1 { font-size: 28px; color: var(--dark); margin-bottom: 8px; }
        .main .subtitle { color: #666; margin-bottom: 32px; }
        .endpoint {
            background: #fff; border: 1px solid var(--border); border-radius: 8px;
            padding: 24px; margin-bottom: 20px;
        }
        .endpoint-header { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
        .endpoint-header h3 { font-size: 16px; }
        .endpoint-header code { font-size: 13px; color: #555; background: #f0f0f0; padding: 2px 8px; border-radius: 4px; }
            .endpoint p { color: #666; font-size: 14px; margin-bottom: 12px; }
            .params-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .params-table th { text-align: left; padding: 8px 12px; background: #f8f9fa; border-bottom: 2px solid var(--border); }
        .params-table td { padding: 8px 12px; border-bottom: 1px solid #eee; }
        .params-table .required { color: #dc3545; font-weight: 600; }
        .response-box { background: #1e1e1e; color: #d4d4d4; padding: 16px; border-radius: 6px; font-family: 'Consolas', monospace; font-size: 12px; overflow-x: auto; white-space: pre; margin-top: 12px; }
        .response-box .key { color: #569cd6; }
        .response-box .str { color: #ce9178; }
        .response-box .num { color: #b5cea8; }
        .notice { background: #cce5ff; border-left: 4px solid var(--primary); padding: 16px; border-radius: 4px; margin-bottom: 24px; font-size: 14px; }
        .section-title { font-size: 22px; color: var(--dark); margin: 40px 0 16px; padding-top: 20px; border-top: 1px solid var(--border); }
        pre { background: #f4f4f4; padding: 12px; border-radius: 4px; overflow-x: auto; font-size: 12px; }
        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main { margin-left: 0; padding: 16px; }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h2><i class="bx bxs-tooth"></i> Grin Dental</h2>
            <p>API Documentation v1.0</p>
        </div>
        <div class="nav-section">
            <h3>Getting Started</h3>
            <a href="#base-url" class="nav-item">Base URL</a>
            <a href="#authentication" class="nav-item">Authentication</a>
            <a href="#errors" class="nav-item">Error Handling</a>
            <a href="#pagination" class="nav-item">Pagination</a>
        </div>
        <div class="nav-section">
            <h3>Auth</h3>
            <a href="#register" class="nav-item">Register</a>
            <a href="#login" class="nav-item">Login</a>
            <a href="#logout" class="nav-item">Logout</a>
            <a href="#forgot-password" class="nav-item">Forgot Password</a>
            <a href="#profile" class="nav-item">Profile</a>
        </div>
        <div class="nav-section">
            <h3>Dentist App</h3>
            <a href="#dentist-dashboard" class="nav-item">Dashboard</a>
            <a href="#dentist-appointments" class="nav-item">Appointments</a>
            <a href="#dentist-patients" class="nav-item">Patients</a>
            <a href="#dentist-notes" class="nav-item">Treatment Notes</a>
            <a href="#dentist-schedule" class="nav-item">Schedule</a>
        </div>
        <div class="nav-section">
            <h3>Patient App</h3>
            <a href="#patient-dashboard" class="nav-item">Dashboard</a>
            <a href="#patient-dentists" class="nav-item">Find Dentists</a>
            <a href="#patient-appointments" class="nav-item">Appointments</a>
            <a href="#patient-records" class="nav-item">Medical Records</a>
            <a href="#patient-payments" class="nav-item">Payments</a>
        </div>
        <div class="nav-section">
            <h3>Shared</h3>
            <a href="#chat" class="nav-item">Chat</a>
            <a href="#notifications" class="nav-item">Notifications</a>
            <a href="#upload" class="nav-item">File Upload</a>
        </div>
    </div>

    <div class="main">
        <h1>Grin Dental API</h1>
        <p class="subtitle">RESTful API for mobile applications</p>

        <div class="notice">
            <strong>Base URL:</strong> <code><?= $baseUrl ?></code><br>
            All endpoints are prefixed with <code>/api/v1/</code>. All request/response bodies are JSON.
        </div>

        <div class="section-title" id="base-url">Base URL &amp; Versioning</div>
        <div class="endpoint">
            <p>All API requests go through the unified router:<br>
            <code>POST/GET /api/v1/index.php?module={module}&action={action}</code></p>
            <p>Modules: <code>auth</code>, <code>dentist</code>, <code>patient</code>, <code>chat</code>, <code>notifications</code>, <code>upload</code></p>
        </div>

        <div class="section-title" id="authentication">Authentication</div>
        <div class="endpoint">
            <p>All authenticated endpoints require an <code>Authorization</code> header:<br>
            <code>Authorization: Bearer {token}</code></p>
            <p>Tokens are returned on login/register and must be included in every request. Tokens expire after 30 days.</p>
        </div>

        <div class="section-title" id="errors">Error Responses</div>
        <div class="endpoint">
            <div class="response-box">{
    <span class="key">"status"</span>: <span class="str">"error"</span>,
    <span class="key">"message"</span>: <span class="str">"Error description"</span>,
    <span class="key">"error_code"</span>: <span class="str">"UNAUTHORIZED"</span>
}</div>
        </div>

        <div class="section-title" id="pagination">Pagination</div>
        <div class="endpoint">
            <p>List endpoints return paginated results:</p>
            <div class="response-box">{
    <span class="key">"status"</span>: <span class="str">"success"</span>,
    <span class="key">"data"</span>: [...],
    <span class="key">"pagination"</span>: {
        <span class="key">"current_page"</span>: <span class="num">1</span>,
        <span class="key">"per_page"</span>: <span class="num">20</span>,
        <span class="key">"total"</span>: <span class="num">150</span>,
        <span class="key">"total_pages"</span>: <span class="num">8</span>
    }
}</div>
        </div>

        <!-- ===== AUTH ===== -->
        <div class="section-title" id="auth">Auth</div>

        <div class="endpoint" id="register">
            <div class="endpoint-header">
                <span class="method post">POST</span>
                <h3>Register</h3>
                <code>action=register</code>
            </div>
            <p>Create a new patient account.</p>
            <table class="params-table">
                <tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr>
                <tr><td><code>name</code></td><td>string</td><td class="required">Yes</td><td>Full name</td></tr>
                <tr><td><code>email</code></td><td>string</td><td class="required">Yes</td><td>Email address</td></tr>
                <tr><td><code>password</code></td><td>string</td><td class="required">Yes</td><td>Min 6 characters</td></tr>
                <tr><td><code>phone</code></td><td>string</td><td>No</td><td>Phone number</td></tr>
                <tr><td><code>gender</code></td><td>string</td><td>No</td><td>male/female</td></tr>
                <tr><td><code>date_of_birth</code></td><td>date</td><td>No</td><td>YYYY-MM-DD</td></tr>
                <tr><td><code>device_token</code></td><td>string</td><td>No</td><td>FCM push token</td></tr>
            </table>
        </div>

        <div class="endpoint" id="login">
            <div class="endpoint-header">
                <span class="method post">POST</span>
                <h3>Login</h3>
                <code>action=login</code>
            </div>
            <p>Authenticate and receive a token.</p>
            <table class="params-table">
                <tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr>
                <tr><td><code>email</code></td><td>string</td><td class="required">Yes</td><td>Email address</td></tr>
                <tr><td><code>password</code></td><td>string</td><td class="required">Yes</td><td>Password</td></tr>
                <tr><td><code>device_token</code></td><td>string</td><td>No</td><td>FCM push token</td></tr>
                <tr><td><code>device_type</code></td><td>string</td><td>No</td><td>android/ios/web</td></tr>
                <tr><td><code>device_name</code></td><td>string</td><td>No</td><td>Device name</td></tr>
            </table>
            <div class="response-box">{
    <span class="key">"status"</span>: <span class="str">"success"</span>,
    <span class="key">"data"</span>: {
        <span class="key">"token"</span>: <span class="str">"a1b2c3d4e5..."</span>,
        <span class="key">"user"</span>: {
            <span class="key">"id"</span>: <span class="num">1</span>,
            <span class="key">"name"</span>: <span class="str">"Ahmed Khan"</span>,
            <span class="key">"role"</span>: <span class="str">"patient"</span>
        }
    }
}</div>
        </div>

        <div class="endpoint" id="logout">
            <div class="endpoint-header">
                <span class="method post">POST</span>
                <h3>Logout</h3>
                <code>action=logout</code>
            </div>
            <p>Revoke the current token. Requires authentication.</p>
        </div>

        <div class="endpoint" id="forgot-password">
            <div class="endpoint-header">
                <span class="method post">POST</span>
                <h3>Forgot Password</h3>
                <code>action=forgot-password</code>
            </div>
            <p>Send password reset email.</p>
            <table class="params-table">
                <tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr>
                <tr><td><code>email</code></td><td>string</td><td class="required">Yes</td><td>Registered email</td></tr>
            </table>
        </div>

        <div class="endpoint" id="profile">
            <div class="endpoint-header">
                <span class="method get">GET</span> <span class="method put">PUT</span>
                <h3>Profile</h3>
                <code>action=profile</code>
            </div>
            <p>GET: Retrieve current user profile. PUT: Update profile fields (name, phone, profile_image, etc).</p>
        </div>

        <!-- ===== DENTIST APP ===== -->
        <div class="section-title" id="dentist">Dentist App</div>

        <div class="endpoint" id="dentist-dashboard">
            <div class="endpoint-header">
                <span class="method get">GET</span>
                <h3>Dentist Dashboard</h3>
                <code>module=dentist&action=dashboard</code>
            </div>
            <p>Returns today's stats: total appointments, completed, pending, revenue, upcoming appointments.</p>
        </div>

        <div class="endpoint" id="dentist-appointments">
            <div class="endpoint-header">
                <span class="method get">GET</span> <span class="method put">PUT</span>
                <h3>Dentist Appointments</h3>
                <code>module=dentist&action=appointments</code>
            </div>
            <p>GET: List appointments with optional date/status filter. PUT: Update appointment status (completed, cancelled, no_show, confirmed).</p>
            <table class="params-table">
                <tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr>
                <tr><td><code>date</code></td><td>date</td><td>No</td><td>Filter by date (YYYY-MM-DD)</td></tr>
                <tr><td><code>status</code></td><td>string</td><td>No</td><td>Filter by status</td></tr>
                <tr><td><code>page</code></td><td>int</td><td>No</td><td>Page number (default: 1)</td></tr>
            </table>
        </div>

        <div class="endpoint" id="dentist-patients">
            <div class="endpoint-header">
                <span class="method get">GET</span>
                <h3>Dentist Patients</h3>
                <code>module=dentist&action=patients</code>
            </div>
            <p>List all patients with optional search.</p>
        </div>

        <div class="endpoint" id="dentist-notes">
            <div class="endpoint-header">
                <span class="method get">GET</span> <span class="method post">POST</span>
                <h3>Treatment Notes</h3>
                <code>module=dentist&action=treatment-notes</code>
            </div>
            <p>GET: View notes for a patient. POST: Create a new treatment note.</p>
            <table class="params-table">
                <tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr>
                <tr><td><code>patient_id</code></td><td>int</td><td class="required">Yes</td><td>Patient ID</td></tr>
                <tr><td><code>appointment_id</code></td><td>int</td><td>No</td><td>Related appointment</td></tr>
                <tr><td><code>diagnosis</code></td><td>string</td><td class="required">Yes</td><td>Diagnosis text</td></tr>
                <tr><td><code>treatment_performed</code></td><td>string</td><td>No</td><td>Treatment description</td></tr>
                <tr><td><code>notes</code></td><td>string</td><td>No</td><td>Additional notes</td></tr>
            </table>
        </div>

        <div class="endpoint" id="dentist-schedule">
            <div class="endpoint-header">
                <span class="method get">GET</span> <span class="method post">POST</span>
                <h3>Dentist Schedule</h3>
                <code>module=dentist&action=schedule</code>
            </div>
            <p>GET: View own schedule. POST: Update working hours / add exceptions (day_off, holiday).</p>
        </div>

        <!-- ===== PATIENT APP ===== -->
        <div class="section-title" id="patient">Patient App</div>

        <div class="endpoint" id="patient-dashboard">
            <div class="endpoint-header">
                <span class="method get">GET</span>
                <h3>Patient Dashboard</h3>
                <code>module=patient&action=dashboard</code>
            </div>
            <p>Returns upcoming appointments, recent prescriptions, and stats.</p>
        </div>

        <div class="endpoint" id="patient-dentists">
            <div class="endpoint-header">
                <span class="method get">GET</span>
                <h3>Find Dentists</h3>
                <code>module=patient&action=dentists</code>
            </div>
            <p>Search and filter available dentists.</p>
            <table class="params-table">
                <tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr>
                <tr><td><code>search</code></td><td>string</td><td>No</td><td>Search by name or specialization</td></tr>
                <tr><td><code>specialization</code></td><td>string</td><td>No</td><td>Filter by specialization</td></tr>
                <tr><td><code>page</code></td><td>int</td><td>No</td><td>Page number</td></tr>
            </table>
        </div>

        <div class="endpoint" id="patient-appointments">
            <div class="endpoint-header">
                <span class="method get">GET</span> <span class="method post">POST</span> <span class="method put">PUT</span> <span class="method delete">DELETE</span>
                <h3>Patient Appointments</h3>
                <code>module=patient&action=appointments</code>
            </div>
            <p>
                <strong>GET:</strong> List appointments (use <code>history=1</code> for past).<br>
                <strong>POST:</strong> Book new appointment (dentist_id, treatment_id, appointment_date, start_time, end_time).<br>
                <strong>PUT:</strong> Reschedule. <strong>DELETE:</strong> Cancel.
            </p>
        </div>

        <div class="endpoint" id="patient-records">
            <div class="endpoint-header">
                <span class="method get">GET</span>
                <h3>Medical Records</h3>
                <code>module=patient&action=medical-records</code>
            </div>
            <p>View own medical records and history.</p>
        </div>

        <div class="endpoint" id="patient-payments">
            <div class="endpoint-header">
                <span class="method get">GET</span> <span class="method post">POST</span>
                <h3>Payments</h3>
                <code>module=patient&action=payments</code>
            </div>
            <p>GET: View payment history. POST: Process online payment (invoice_id, amount, gateway).</p>
            <table class="params-table">
                <tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr>
                <tr><td><code>invoice_id</code></td><td>int</td><td class="required">Yes (POST)</td><td>Invoice to pay</td></tr>
                <tr><td><code>amount</code></td><td>decimal</td><td class="required">Yes (POST)</td><td>Payment amount</td></tr>
                <tr><td><code>gateway</code></td><td>string</td><td class="required">Yes (POST)</td><td>stripe/jazzcash/easypaisa</td></tr>
            </table>
        </div>

        <!-- ===== SHARED ===== -->
        <div class="section-title" id="shared">Shared Features</div>

        <div class="endpoint" id="chat">
            <div class="endpoint-header">
                <span class="method get">GET</span> <span class="method post">POST</span>
                <h3>Chat</h3>
                <code>module=chat&action={sub_action}</code>
            </div>
            <p>
                <strong>conversations</strong> (GET): List chat conversations.<br>
                <strong>messages/{id}</strong> (GET): Get messages for conversation.<br>
                <strong>messages</strong> (POST): Send a message (conversation_id, message, message_type, file_path).<br>
                <strong>read</strong> (POST): Mark conversation as read (conversation_id).
            </p>
        </div>

        <div class="endpoint" id="notifications">
            <div class="endpoint-header">
                <span class="method get">GET</span> <span class="method post">POST</span>
                <h3>Notifications</h3>
                <code>module=notifications&action={sub_action}</code>
            </div>
            <p>
                <strong>list</strong> (GET): Get all notifications.<br>
                <strong>unread-count</strong> (GET): Get unread count.<br>
                <strong>read</strong> (POST): Mark one as read (id).<br>
                <strong>read-all</strong> (POST): Mark all as read.
            </p>
        </div>

        <div class="endpoint" id="upload">
            <div class="endpoint-header">
                <span class="method post">POST</span>
                <h3>File Upload</h3>
                <code>module=upload&action={type}</code>
            </div>
            <p>
                Upload files as <code>multipart/form-data</code> with field <code>file</code>.<br>
                <strong>image</strong>: Upload profile image (max 10MB, JPEG/PNG/GIF/WEBP).<br>
                <strong>file</strong>: Upload medical document (PDF/DOC/DOCX + images).
            </p>
            <table class="params-table">
                <tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr>
                <tr><td><code>file</code></td><td>file</td><td class="required">Yes</td><td>File to upload</td></tr>
                <tr><td><code>type</code></td><td>string</td><td>No</td><td>xray/report/prescription/image/document/other</td></tr>
                <tr><td><code>patient_id</code></td><td>int</td><td>Yes (file)</td><td>Patient ID for medical files</td></tr>
                <tr><td><code>title</code></td><td>string</td><td>No</td><td>File title</td></tr>
            </table>
        </div>

        <div style="margin-top: 48px; padding-top: 24px; border-top: 1px solid var(--border); text-align: center; color: #999; font-size: 13px;">
            &copy; <?= date('Y') ?> Grin Dental Clinic &mdash; API Documentation
        </div>
    </div>

    <script>
        document.querySelectorAll('.nav-item').forEach(a => {
            a.addEventListener('click', function(e) {
                document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>
