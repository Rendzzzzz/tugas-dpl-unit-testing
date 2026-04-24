<?php
session_start();
include 'config.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = 'admin';
    $password = 'admin';

    $stmt = $conn->prepare("SELECT id, password FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($db_id, $db_password);
        $stmt->fetch();
        if (password_verify($password, $db_password)) {
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $username;
            header("Location: index.php");
            exit();
        } else {
            $error = "Password salah.";
        }
    } else {
        $error = "Username tidak ditemukan.";
    }
    $stmt->close();
}
?>
