<?php
// Start session
session_start();

// If the carer is logged in, destroy the session and redirect to login page
if (isset($_SESSION['carer_id'])) {
    // Unset all of the session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to login page
    header("Location: carerlogin.php");
    exit();
} else {
    // If the carer is not logged in redirect
    header("Location: carerlogin.php");
    exit();
}
?>
