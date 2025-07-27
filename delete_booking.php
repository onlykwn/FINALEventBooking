<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Restrict delete to staff/admin only
if ($_SESSION['role'] === 'student') {
    $_SESSION['swal'] = [
        'icon' => 'error',
        'title' => 'Access Denied',
        'text' => 'Students are not allowed to delete bookings.'
    ];
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM bookings WHERE booking_id = $id");

    $_SESSION['swal'] = [
        'icon' => 'success',
        'title' => 'Booking Deleted',
        'text' => 'The booking was successfully deleted.'
    ];
}

header("Location: index.php");
exit();
?>
