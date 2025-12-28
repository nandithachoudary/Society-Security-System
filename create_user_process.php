<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}

require 'db_connect.php';

if (
    $_SERVER["REQUEST_METHOD"] == "POST" &&
    !empty($_POST['full_name']) &&
    !empty($_POST['username']) &&
    !empty($_POST['password']) &&
    !empty($_POST['role'])
) {
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $flat_id = $_POST['flat_id'] ?? null;

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    if ($role === 'supervisor') {
        $stmt = $conn->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password_hash, $role);
        if ($stmt->execute()) {
            header("Location: admin_dashboard.php?status=user_success");
        } else {
            header("Location: admin_dashboard.php?status=user_error");
        }
        $stmt->close();
    }
    elseif ($role === 'resident' && !empty($flat_id)) {
        $conn->begin_transaction();
        try {
            $stmt_resident = $conn->prepare("INSERT INTO residents (name, flat_id) VALUES (?, ?)");
            $stmt_resident->bind_param("si", $full_name, $flat_id);
            $stmt_resident->execute();

            $new_resident_id = $conn->insert_id;

            $stmt_user = $conn->prepare("INSERT INTO users (username, password_hash, role, resident_id) VALUES (?, ?, ?, ?)");
            $stmt_user->bind_param("sssi", $username, $password_hash, $role, $new_resident_id);
            $stmt_user->execute();

            $conn->commit();
            header("Location: admin_dashboard.php?status=user_success");

        } catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            header("Location: admin_dashboard.php?status=user_error");
        }
    } else {
        header("Location: admin_dashboard.php?status=flat_required");
    }
} else {
    header("Location: admin_dashboard.php?status=missing_fields");
}

$conn->close();
exit();
?>