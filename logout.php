<?php
session_start();
session_unset();
session_destroy();

$referer = $_SERVER['HTTP_REFERER'] ?? '';
if (strpos($referer, '/admin/') !== false) {
    header('Location: ../index.php');
} else {
    header('Location: index.php');
}
exit;
