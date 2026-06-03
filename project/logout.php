<?php

function logoutUser()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Hapus semua data session
    $_SESSION = [];

    // Destroy session
    session_destroy();

    return "login.php";
}

if (!defined('PHPUNIT_RUNNING')) {
    $redirect = logoutUser();

    header("Location: $redirect");
    exit();
}
?>