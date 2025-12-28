<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'supervisor') {
    die("Access Denied.");
}
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['visitor_name']) && isset($_POST['flat_id'])) {
    
    $visitor_name = $_POST['visitor_name'];
    $flat_id = $_POST['flat_id'];
    $contact_number = !empty($_POST['contact_number']) ? $_POST['contact_number'] : NULL;

    if (!preg_match('/^[a-zA-Z ]+$/', $visitor_name)) {
        header("Location: supervisor_dashboard.php?status=invalid_name");
        exit();
    }
    if ($contact_number !== NULL && !preg_match('/^[6-9][0-9]{9}$/', $contact_number)) {
        header("Location: supervisor_dashboard.php?status=invalid_phone");
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO visitors (name, contact_number, request_time, flat_id, status) VALUES (?, ?, NOW(), ?, 'pending')");
    
    if ($stmt === false) {
        header("Location: supervisor_dashboard.php?status=error");
        exit();
    }

    $stmt->bind_param("ssi", $visitor_name, $contact_number, $flat_id);

    if ($stmt->execute()) {
        header("Location: supervisor_dashboard.php?status=success");
    } else {
        header("Location: supervisor_dashboard.php?status=error");
    }
    
    $stmt->close();
    $conn->close();

} else {
    header("Location: supervisor_dashboard.php?status=error");
}
exit();
?>