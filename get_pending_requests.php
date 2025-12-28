<?php
session_start();
header('Content-Type: application/json');
require '../db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'resident' || !isset($_SESSION['resident_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$stmt_flat = $conn->prepare("SELECT flat_id FROM residents WHERE resident_id = ?");
$stmt_flat->bind_param("i", $_SESSION['resident_id']);
$stmt_flat->execute();
$result_flat = $stmt_flat->get_result();
if ($result_flat->num_rows === 0) {
    echo json_encode(['error' => 'Resident not found']);
    exit();
}
$resident_data = $result_flat->fetch_assoc();
$flat_id = $resident_data['flat_id'];

$stmt = $conn->prepare("SELECT visitor_id, name, contact_number FROM visitors WHERE flat_id = ? AND status = 'pending'");
$stmt->bind_param("i", $flat_id);
$stmt->execute();
$result = $stmt->get_result();

$visitors = [];
while ($row = $result->fetch_assoc()) {
    $visitors[] = $row;
}

echo json_encode($visitors);
?>