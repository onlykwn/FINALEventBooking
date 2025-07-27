<?php
session_start();
require_once __DIR__ . '/config.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        isset($_POST['facility_id'], $_POST['start_date'], $_POST['time_in'], $_POST['time_out'], $_POST['booked_by'], $_POST['description']) &&
        !empty($_POST['facility_id']) && !empty($_POST['start_date']) && !empty($_POST['time_in']) &&
        !empty($_POST['time_out']) && !empty($_POST['booked_by']) && !empty($_POST['description'])
    ) {
        $facility_id = intval($_POST['facility_id']);
        $start_date = $_POST['start_date'];
        $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : $start_date;
        $time_in = $_POST['time_in'];
        $time_out = $_POST['time_out'];
        $booked_by = trim($_POST['booked_by']);
        $description = trim($_POST['description']);
        $status = 'Booked';

        try {
            $begin = new DateTime($start_date);
            $end = new DateTime($end_date);
            $end = $end->modify('+1 day');
            $interval = new DateInterval('P1D');
            $daterange = new DatePeriod($begin, $interval, $end);

            $conflict_found = false;

            foreach ($daterange as $date) {
                $dateStr = $date->format("Y-m-d");

                $conflict_sql = "
                    SELECT * FROM bookings
                    WHERE facility_id = ?
                    AND start_date = ?
                    AND (
                        (time_in < ? AND time_out > ?) OR
                        (time_in >= ? AND time_in < ?)
                    )
                ";

                $stmt = $conn->prepare($conflict_sql);
                $stmt->bind_param("isssss", $facility_id, $dateStr, $time_out, $time_in, $time_in, $time_out);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $conflict_found = true;
                    break;
                }
            }

            if ($conflict_found) {
                $_SESSION['swal'] = [
                    'icon' => 'error',
                    'title' => 'Booking Conflict',
                    'text' => 'The selected facility is already booked for the chosen time.'
                ];
            } else {
                $insert_sql = "
                    INSERT INTO bookings (facility_id, start_date, end_date, time_in, time_out, booked_by, description, status, user_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insert_sql);

                foreach ($daterange as $date) {
                    $dateStr = $date->format("Y-m-d");
                    $user_id = $_SESSION['user_id'];

                    $stmt->bind_param("isssssssi", $facility_id, $dateStr, $dateStr, $time_in, $time_out, $booked_by, $description, $status, $user_id);
                    $stmt->execute();
                }

                $_SESSION['swal'] = [
                    'icon' => 'success',
                    'title' => 'Booking Successful',
                    'text' => 'The facility has been booked successfully.'
                ];
            }
        } catch (Exception $e) {
            $_SESSION['swal'] = [
                'icon' => 'error',
                'title' => 'Error Occurred',
                'text' => $e->getMessage()
            ];
        }
    } else {
        $_SESSION['swal'] = [
            'icon' => 'error',
            'title' => 'Missing Fields',
            'text' => 'Please complete all required fields.'
        ];
    }

    header("Location: add_booking.php");
    exit();
} else {
    header("Location: add_booking.php");
    exit();
}
