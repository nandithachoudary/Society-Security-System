<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, password_hash, role, resident_id FROM users WHERE username = ?");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $password_hash, $role, $resident_id);
        $stmt->fetch();

        if (password_verify($password, $password_hash)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['role'] = $role;
            $_SESSION['resident_id'] = $resident_id;

            if ($role === 'resident') {
                header("Location: resident_dashboard.php");
                exit();
            } elseif ($role === 'supervisor') {
                header("Location: supervisor_dashboard.php");
                exit();
            } elseif ($role === 'admin') {
                header("Location: admin_dashboard.php");
                exit();
            } else {
                header("Location: index.php?error=invalid_role");
                exit();
            }
        }
    }
    
    header("Location: index.php?error=1");
    exit();
}
?>