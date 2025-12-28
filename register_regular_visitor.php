<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'supervisor') {
    die("Access Denied.");
}
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['name']) && !empty($_POST['flat_id'])) {
    $name = $_POST['name'];
    $role = $_POST['role'];
    $flat_id = $_POST['flat_id'];

    do {
        $security_code = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        $stmt_check = $conn->prepare("SELECT regular_visitor_id FROM regular_visitors WHERE security_code = ?");
        $stmt_check->bind_param("s", $security_code);
        $stmt_check->execute();
        $stmt_check->store_result();
    } while ($stmt_check->num_rows > 0);

    $stmt = $conn->prepare("INSERT INTO regular_visitors (name, role, flat_id, security_code) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $name, $role, $flat_id, $security_code);

    if ($stmt->execute()) {
        header("Location: supervisor_dashboard.php?reg_status=success&new_code=" . $security_code);
    } else {
        header("Location: supervisor_dashboard.php?reg_status=error");
    }
    $stmt->close();
} else {
    header("Location: supervisor_dashboard.php");
}
exit();
?>