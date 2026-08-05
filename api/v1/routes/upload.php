<?php
/**
 * Upload Routes
 * POST upload/file - Upload medical file
 * POST upload/image - Upload profile image
 */

$uploadDir = __DIR__ . '/../../uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

switch ($action) {
    case 'file':
    case 'image':
        if ($method !== 'POST') {
            ApiResponse::error("POST required");
        }

        if (empty($_FILES['file'])) {
            ApiResponse::error("No file uploaded");
        }

        $file = $_FILES['file'];
        $uploadType = $input['type'] ?? 'document';
        $patientId = $input['patient_id'] ?? null;
        $title = $input['title'] ?? $file['name'];

        // Validate file
        $allowedTypes = [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            'application/pdf',
            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];

        if (!in_array($file['type'], $allowedTypes)) {
            ApiResponse::error("File type not allowed: " . $file['type']);
        }

        if ($file['size'] > 10 * 1024 * 1024) {
            ApiResponse::error("File too large (max 10MB)");
        }

        // Generate unique filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('med_') . '_' . time() . '.' . $ext;
        $subDir = $action === 'image' ? 'profiles/' : 'medical/';
        $filepath = $uploadDir . $subDir . $filename;

        if (!file_exists($uploadDir . $subDir)) {
            mkdir($uploadDir . $subDir, 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Save to database
            $relativePath = 'uploads/' . $subDir . $filename;

            if ($action === 'image') {
                // Profile image
                $table = $user['role'] === 'dentist' ? 'users' : 'users';
                $stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
                $stmt->execute([$relativePath, $user['user_id']]);
                ApiResponse::success([
                    'file_path' => $relativePath,
                    'file_name' => $filename
                ], "Image uploaded");
            } else {
                // Medical file
                if (!$patientId) ApiResponse::error("Patient ID required for medical files");

                $stmt = $pdo->prepare("INSERT INTO medical_files (patient_id, uploaded_by, upload_type, title, file_path, file_name, file_size, mime_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $patientId, $user['user_id'], $uploadType,
                    $title, $relativePath, $file['name'],
                    $file['size'], $file['type']
                ]);

                ApiResponse::success([
                    'id' => $pdo->lastInsertId(),
                    'file_path' => $relativePath,
                    'file_name' => $file['name']
                ], "File uploaded");
            }
        } else {
            ApiResponse::error("Failed to upload file");
        }
        break;

    case 'list':
        if ($method === 'GET') {
            $patientId = $input['patient_id'] ?? null;
            $type = $input['type'] ?? null;

            $where = "1=1";
            $params = [];

            if ($patientId) {
                $where .= " AND patient_id = ?";
                $params[] = $patientId;
            }
            if ($type) {
                $where .= " AND upload_type = ?";
                $params[] = $type;
            }

            $stmt = $pdo->prepare("SELECT mf.*, u.name as uploaded_by_name FROM medical_files mf JOIN users u ON mf.uploaded_by = u.id WHERE $where ORDER BY mf.created_at DESC");
            $stmt->execute($params);
            ApiResponse::success($stmt->fetchAll());
        }
        break;

    default:
        ApiResponse::error("Invalid upload endpoint", 404);
}
