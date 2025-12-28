<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'supervisor') {
    die("Access Denied.");
}

require 'db_connect.php';

if (isset($_GET['visitor_id'])) {
    $visitor_id = $_GET['visitor_id'];
    $stmt = $conn->prepare("UPDATE visitors SET status = 'checked_out', check_out_time = NOW() WHERE visitor_id = ? AND status = 'approved'");
    $stmt->bind_param("i", $visitor_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: supervisor_dashboard.php");
exit();
?>