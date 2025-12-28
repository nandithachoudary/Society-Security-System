<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'supervisor') {
    die("Access Denied.");
}
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['security_code'])) {
    $security_code = $_POST['security_code'];

    $stmt_find = $conn->prepare("SELECT regular_visitor_id, name FROM regular_visitors WHERE security_code = ? AND status = 'active'");
    $stmt_find->bind_param("s", $security_code);
    $stmt_find->execute();
    $result = $stmt_find->get_result();

    if ($result->num_rows === 1) {
        $visitor = $result->fetch_assoc();
        $visitor_id = $visitor['regular_visitor_id'];
        $visitor_name = $visitor['name'];

        $stmt_check = $conn->prepare("SELECT log_id FROM attendance_log WHERE regular_visitor_id = ? AND check_out_time IS NULL");
        $stmt_check->bind_param("i", $visitor_id);
        $stmt_check->execute();
        $log_result = $stmt_check->get_result();

        if ($log_result->num_rows > 0) {
            $log_entry = $log_result->fetch_assoc();
            $log_id = $log_entry['log_id'];
            $stmt_out = $conn->prepare("UPDATE attendance_log SET check_out_time = NOW() WHERE log_id = ?");
            $stmt_out->bind_param("i", $log_id);
            $stmt_out->execute();
            header("Location: supervisor_dashboard.php?log_status=checked_out&name=" . urlencode($visitor_name));
        } else {
            $stmt_in = $conn->prepare("INSERT INTO attendance_log (regular_visitor_id, check_in_time) VALUES (?, NOW())");
            $stmt_in->bind_param("i", $visitor_id);
            $stmt_in->execute();
            header("Location: supervisor_dashboard.php?log_status=checked_in&name=" . urlencode($visitor_name));
        }
    } else {
        header("Location: supervisor_dashboard.php?log_status=invalid_code");
    }
} else {
    header("Location: supervisor_dashboard.php");
}
exit();
?>