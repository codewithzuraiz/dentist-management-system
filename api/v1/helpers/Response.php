<?php
/**
 * API Response Helper
 */

class ApiResponse {
    
    public static function success($data = null, $message = 'Success', $code = 200) {
        http_response_code($code);
        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }

    public static function error($message = 'Error', $code = 400, $errors = null) {
        http_response_code($code);
        $response = [
            'status' => 'error',
            'message' => $message
        ];
        if ($errors) {
            $response['errors'] = $errors;
        }
        echo json_encode($response);
        exit;
    }

    public static function unauthorized($message = 'Unauthorized') {
        self::error($message, 401);
    }

    public static function notFound($message = 'Not found') {
        self::error($message, 404);
    }

    public static function paginated($data, $total, $page, $perPage) {
        self::success([
            'items' => $data,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => ceil($total / $perPage)
            ]
        ]);
    }
}
