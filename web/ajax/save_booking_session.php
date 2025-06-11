<?php
// Lưu hết vào Session
include('../Core/Config/require_web.php');
session_start();

if (isset($_POST['bookingData'])) {
    $_SESSION['booking_data'] = json_decode($_POST['bookingData'], true);
    echo json_encode(['success' => true]);
    exit;
}
echo json_encode(['success' => false]);



