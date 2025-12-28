<?php
session_start();
header('Content-Type: application/json');
require '../db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'resident') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$visitor_id = $data['visitor_id'];

if (!empty($visitor_id)) {
    $stmt = $conn->prepare("UPDATE visitors SET status = 'approved', check_in_time = NOW() WHERE visitor_id = ?");
    $stmt->bind_param("i", $visitor_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database update failed']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid visitor ID']);
}
?>