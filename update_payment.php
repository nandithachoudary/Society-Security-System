<?php
session_start();
if (!isset($_SESSION['role']) || !isset($_SESSION['role'])) {
    die("Access Denied."); 
}
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['maintenance_id']) && !empty($_POST['payment_mode'])) {
    $maintenance_id = $_POST['maintenance_id'];
    $payment_mode = $_POST['payment_mode'];

    $stmt = $conn->prepare("UPDATE maintenance SET status = 'paid', payment_date = CURDATE(), payment_mode = ? WHERE maintenance_id = ? AND status = 'due'");
    $stmt->bind_param("si", $payment_mode, $maintenance_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: admin_dashboard.php");
exit();
?>