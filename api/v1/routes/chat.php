<?php
/**
 * Chat Routes
 * GET chat/conversations - List conversations
 * GET chat/messages/{conversationId} - Get messages
 * POST chat/messages - Send message
 * POST chat/conversations - Create conversation
 */

switch ($action) {
    case 'conversations':
        if ($method === 'GET') {
            if ($user['role'] === 'dentist') {
                $dentistId = $auth->getEntityId($user['user_id'], 'dentist');
                $stmt = $pdo->prepare("SELECT cc.*, u_p.name as patient_name, u_p.profile_image as patient_image FROM chat_conversations cc JOIN patients pt ON cc.patient_id = pt.id JOIN users u_p ON pt.user_id = u_p.id WHERE cc.dentist_id = ? ORDER BY cc.last_message_at DESC");
                $stmt->execute([$dentistId]);
            } else {
                $patientId = $auth->getEntityId($user['user_id'], 'patient');
                $stmt = $pdo->prepare("SELECT cc.*, u_d.name as dentist_name, u_d.profile_image as dentist_image, d.specialization FROM chat_conversations cc JOIN dentists d ON cc.dentist_id = d.id JOIN users u_d ON d.user_id = u_d.id WHERE cc.patient_id = ? ORDER BY cc.last_message_at DESC");
                $stmt->execute([$patientId]);
            }
            ApiResponse::success($stmt->fetchAll());
        } elseif ($method === 'POST') {
            // Create conversation
            if ($user['role'] === 'dentist') {
                $dentistId = $auth->getEntityId($user['user_id'], 'dentist');
                $patientId = $input['patient_id'];
            } else {
                $patientId = $auth->getEntityId($user['user_id'], 'patient');
                $dentistId = $input['dentist_id'];
            }

            $stmt = $pdo->prepare("INSERT INTO chat_conversations (dentist_id, patient_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE id = id");
            $stmt->execute([$dentistId, $patientId]);
            $convId = $pdo->lastInsertId();

            if (!$convId) {
                $stmt = $pdo->prepare("SELECT id FROM chat_conversations WHERE dentist_id = ? AND patient_id = ?");
                $stmt->execute([$dentistId, $patientId]);
                $convId = $stmt->fetch()['id'];
            }

            ApiResponse::success(['conversation_id' => $convId]);
        }
        break;

    case 'messages':
        $convId = $parts[2] ?? null;

        if ($method === 'GET' && $convId) {
            $page = max(1, (int)($input['page'] ?? 1));
            $perPage = 50;
            $offset = ($page - 1) * $perPage;

            $stmt = $pdo->prepare("SELECT * FROM chat_messages WHERE conversation_id = ? ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");
            $stmt->execute([$convId]);
            $messages = $stmt->fetchAll();

            // Mark as read
            $senderType = $user['role'] === 'dentist' ? 'patient' : 'dentist';
            $stmt = $pdo->prepare("UPDATE chat_messages SET is_read = 'yes' WHERE conversation_id = ? AND sender_type = ?");
            $stmt->execute([$convId, $senderType]);

            ApiResponse::success(array_reverse($messages));
        } elseif ($method === 'POST') {
            $required = ['conversation_id', 'message'];
            foreach ($required as $field) {
                if (empty($input[$field])) ApiResponse::error("Missing: $field");
            }

            $senderType = $user['role'] === 'dentist' ? 'dentist' : 'patient';

            $stmt = $pdo->prepare("INSERT INTO chat_messages (conversation_id, sender_type, sender_id, message, message_type, file_path) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $input['conversation_id'], $senderType, $user['user_id'],
                $input['message'], $input['message_type'] ?? 'text',
                $input['file_path'] ?? null
            ]);
            $msgId = $pdo->lastInsertId();

            // Update conversation
            $unreadField = $user['role'] === 'dentist' ? 'unread_patient' : 'unread_dentist';
            $stmt = $pdo->prepare("UPDATE chat_conversations SET last_message = ?, last_message_at = NOW(), $unreadField = $unreadField + 1 WHERE id = ?");
            $stmt->execute([$input['message'], $input['conversation_id']]);

            ApiResponse::success(['message_id' => $msgId], "Message sent");
        }
        break;

    // Mark conversation read
    case 'read':
        if ($method === 'POST') {
            $convId = $input['conversation_id'] ?? null;
            if ($convId) {
                $unreadField = $user['role'] === 'dentist' ? 'unread_dentist' : 'unread_patient';
                $stmt = $pdo->prepare("UPDATE chat_conversations SET $unreadField = 0 WHERE id = ?");
                $stmt->execute([$convId]);
            }
            ApiResponse::success(null, "Marked as read");
        }
        break;

    default:
        ApiResponse::error("Invalid chat endpoint", 404);
}
