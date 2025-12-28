<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}

require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['building_name'])) {
    $building_name = $_POST['building_name'];

    $stmt = $conn->prepare("INSERT INTO buildings (building_name) VALUES (?)");
    $stmt->bind_param("s", $building_name);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php?status=building_success");
    } else {
        header("Location: admin_dashboard.php?status=error");
    }
    $stmt->close();
} else {
    header("Location: admin_dashboard.php");
}
exit();
?>