<?php
/**
 * Notifications Routes
 * GET notifications - List notifications
 * POST notifications/read - Mark as read
 * POST notifications/read-all - Mark all as read
 */

switch ($action) {
    case '':
    case 'list':
        $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 50");
        $stmt->execute([$user['user_id']]);
        ApiResponse::success($stmt->fetchAll());
        break;

    case 'read':
        if ($method === 'POST') {
            $notifId = $input['id'] ?? null;
            if ($notifId) {
                $stmt = $pdo->prepare("UPDATE notifications SET is_read = 'yes' WHERE id = ? AND user_id = ?");
                $stmt->execute([$notifId, $user['user_id']]);
            }
            ApiResponse::success(null, "Marked as read");
        }
        break;

    case 'read-all':
        if ($method === 'POST') {
            $stmt = $pdo->prepare("UPDATE notifications SET is_read = 'yes' WHERE user_id = ? AND is_read = 'no'");
            $stmt->execute([$user['user_id']]);
            ApiResponse::success(null, "All marked as read");
        }
        break;

    case 'unread-count':
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 'no'");
        $stmt->execute([$user['user_id']]);
        ApiResponse::success(['count' => $stmt->fetch()['count']]);
        break;

    default:
        ApiResponse::error("Invalid notifications endpoint", 404);
}
