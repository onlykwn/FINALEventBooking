<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff') {
    $_SESSION['swal'] = [
        'icon' => 'error',
        'title' => 'Access Denied',
        'text' => 'Only staff or admin can access this action.'
    ];
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $booking_id = intval($_GET['id']);

    $result = $conn->query("SELECT status FROM bookings WHERE booking_id = $booking_id");

    if ($result && $result->num_rows > 0) {
        $booking = $result->fetch_assoc();
        $status = $booking['status'];

        if ($status === 'Available') {
            $_SESSION['swal'] = [
                'icon' => 'info',
                'title' => 'Already Available',
                'text' => 'This facility is already marked as available for this day.'
            ];
        } else {
            $conn->query("UPDATE bookings SET status = 'Available' WHERE booking_id = $booking_id");

            $_SESSION['swal'] = [
                'icon' => 'success',
                'title' => 'Marked Available',
                'text' => 'This booking has been marked as available for this date.'
            ];
        }
    } else {
        $_SESSION['swal'] = [
            'icon' => 'error',
            'title' => 'Booking Not Found',
            'text' => 'No booking record was found for the given ID.'
        ];
    }
}

header("Location: index.php");
exit();
?>
