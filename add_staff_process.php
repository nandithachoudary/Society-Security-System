<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'supervisor') {
    die("Access Denied.");
}

require 'db_connect.php';

if (
    $_SERVER["REQUEST_METHOD"] == "POST" &&
    !empty($_POST['name']) &&
    !empty($_POST['role'])
) {
    $name = $_POST['name'];
    $role = $_POST['role'];
    $contact_number = !empty($_POST['contact_number']) ? $_POST['contact_number'] : NULL;
    $joining_date = !empty($_POST['joining_date']) ? $_POST['joining_date'] : NULL;

    if (!preg_match('/^[a-zA-Z ]+$/', $name)) {
        header("Location: supervisor_dashboard.php?staff_status=invalid_name");
        exit();
    }
    if ($contact_number !== NULL && !preg_match('/^[6-9][0-9]{9}$/', $contact_number)) {
        header("Location: supervisor_dashboard.php?staff_status=invalid_phone");
        exit();
    }
    $stmt = $conn->prepare("INSERT INTO staff (name, role, contact_number, joining_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $role, $contact_number, $joining_date);

    if ($stmt->execute()) {
        header("Location: supervisor_dashboard.php?staff_status=success");
    } else {
        header("Location: supervisor_dashboard.php?staff_status=error");
    }
    
    $stmt->close();
} else {
    header("Location: supervisor_dashboard.php?staff_status=missing_fields");
}

$conn->close();
exit();
?>