<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}
require 'db_connect.php';

if (
    $_SERVER["REQUEST_METHOD"] == "POST" &&
    !empty($_POST['month']) &&
    !empty($_POST['year']) &&
    !empty($_POST['amount']) &&
    !empty($_POST['due_date'])
) {
    $month = $_POST['month'];
    $year = $_POST['year'];
    $amount = $_POST['amount'];
    $due_date = $_POST['due_date'];

    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM maintenance WHERE month = ? AND year = ?");
    $check_stmt->bind_param("ii", $month, $year);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count > 0) {
        header("Location: admin_dashboard.php?maintenance_status=already_exists");
        exit();
    }

    $flats_result = $conn->query("SELECT flat_id FROM residents");
    $flat_ids = [];
    while ($row = $flats_result->fetch_assoc()) {
        $flat_ids[] = $row['flat_id'];
    }

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("INSERT INTO maintenance (flat_id, amount, month, year, due_date, status) VALUES (?, ?, ?, ?, ?, 'due')");
        foreach ($flat_ids as $flat_id) {
            $stmt->bind_param("idiss", $flat_id, $amount, $month, $year, $due_date);
            $stmt->execute();
        }
        $conn->commit();
        header("Location: admin_dashboard.php?maintenance_status=success");
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        header("Location: admin_dashboard.php?maintenance_status=error");
    }
    $stmt->close();
} else {
    header("Location: admin_dashboard.php?maintenance_status=missing_fields");
}
exit();
?>