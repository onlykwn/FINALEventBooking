<?php
include 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $booking_id = $_POST["booking_id"];
    $facility_id = $_POST["facility_id"];
    $booked_by = $_POST["booked_by"];
    $description = $_POST["description"];
    $start_date = $_POST["date"];
    $end_date = $_POST["end_date"];
    $time_in = $_POST["time_in"];
    $time_out = $_POST["time_out"];

    // ✅ Count existing days before deletion
    $old_result = $conn->query("SELECT COUNT(*) AS total FROM bookings WHERE booking_id = $booking_id");
    $old_row = $old_result->fetch_assoc();
    $old_days = (int)$old_row['total'];

    // ✅ Delete original entries
    $conn->query("DELETE FROM bookings WHERE booking_id = $booking_id");

    // ✅ Insert new dates using same booking_id
    $stmt = $conn->prepare("INSERT INTO bookings 
        (booking_id, facility_id, date, end_date, time_in, time_out, booked_by, description, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Booked')");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $current = strtotime($start_date);
    $end = strtotime($end_date);
    $new_days = 0;

    while ($current <= $end) {
        $booking_date = date("Y-m-d", $current);
        $stmt->bind_param("iissssss", $booking_id, $facility_id, $booking_date, $end_date, $time_in, $time_out, $booked_by, $description);
        $stmt->execute();
        $current = strtotime("+1 day", $current);
        $new_days++;
    }

    $stmt->close();

    $_SESSION['swal'] = [
        'icon' => 'success',
        'title' => 'Booking Updated',
        'text' => "Updated to $new_days day(s) from $old_days day(s) booked"
    ];

    header("Location: index.php");
    exit();
}
?>
