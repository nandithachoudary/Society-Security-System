<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}

require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['flat_number']) && !empty($_POST['building_id'])) {
    $flat_number = $_POST['flat_number'];
    $building_id = $_POST['building_id'];

    $stmt = $conn->prepare("INSERT INTO flats (flat_number, building_id) VALUES (?, ?)");
    $stmt->bind_param("si", $flat_number, $building_id);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php?status=flat_success");
    } else {
        header("Location: admin_dashboard.php?status=error");
    }
    $stmt->close();
} else {
    header("Location: admin_dashboard.php");
}
exit();
?>