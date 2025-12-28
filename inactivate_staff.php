<?php
session_start();
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'supervisor' && $_SESSION['role'] !== 'admin')) {
    die("Access Denied.");
}

require 'db_connect.php';

if (isset($_GET['staff_id'])) {
    $staff_id = $_GET['staff_id'];

    $stmt = $conn->prepare("UPDATE staff SET status = 'inactive' WHERE staff_id = ?");
    
    $stmt->bind_param("i", $staff_id);

    $stmt->execute();

    $stmt->close();
}

header("Location: supervisor_dashboard.php?staff_status=inactivated");
exit();
?>