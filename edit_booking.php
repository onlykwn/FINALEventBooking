<?php
session_start();
$showAccessDenied = false;
include 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Check role
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff') {
    $showAccessDenied = true;
}

if (!isset($_GET['id'])) {
    echo "No booking ID provided.";
    exit;
}

$id = $_GET['id'];

// ✅ Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $facility_id = $_POST['facility_id'];
    $start_date = $_POST['start_date'];
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : $start_date;
    $time_in = $_POST['time_in'];
    $time_out = $_POST['time_out'];
    $booked_by = $_POST['booked_by'];
    $description = $_POST['description'];
    $status = 'Booked';

    // Only allow multiple-day re-insert if start != end
    if ($start_date !== $end_date) {
        // Delete original record
        $conn->query("DELETE FROM bookings WHERE booking_id = $id");

        // Re-insert for each date
        $begin = new DateTime($start_date);
        $end = new DateTime($end_date);
        $end->modify('+1 day');
        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod($begin, $interval, $end);

        foreach ($daterange as $date) {
            $dateStr = $date->format("Y-m-d");
            $stmt = $conn->prepare("INSERT INTO bookings (facility_id, start_date, end_date, time_in, time_out, booked_by, description, status)
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssssss", $facility_id, $dateStr, $dateStr, $time_in, $time_out, $booked_by, $description, $status);
            $stmt->execute();
        }

        $_SESSION['swal'] = [
            'icon' => 'success',
            'title' => 'Booking Updated',
            'text' => 'Booking was updated and spread across multiple days.'
        ];
    } else {
        // Just update the current record (no delete)
        $stmt = $conn->prepare("UPDATE bookings SET facility_id = ?, start_date = ?, end_date = ?, time_in = ?, time_out = ?, booked_by = ?, description = ?, status = ?
                                WHERE booking_id = ?");
        $stmt->bind_param("isssssssi", $facility_id, $start_date, $end_date, $time_in, $time_out, $booked_by, $description, $status, $id);
        $stmt->execute();

        $_SESSION['swal'] = [
            'icon' => 'success',
            'title' => 'Booking Updated',
            'text' => 'Booking updated successfully.'
        ];
    }

    header("Location: index.php");
    exit();
}


// ✅ If not submitted yet, fetch booking details
$result = $conn->query("SELECT * FROM bookings WHERE booking_id = '$id'");
$booking = $result->fetch_assoc();

if (!$booking) {
    echo "Booking not found.";
    exit;
}

$facilities = $conn->query("SELECT * FROM facilities");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Booking</title>
    <link rel="stylesheet" href="style.css">
    <style>
    .btn-cancel {
        background: #a17c61;
        color: white;
        padding: 8px 16px;
        border-radius: 5px;
        display: inline-block;
        margin-left: 10px;
        margin-top: 10px;
        text-decoration: none !important;
    }

    .btn-cancel:hover {
        background: #8c6750;
    }

    .form-buttons {
        margin-top: 10px;
        text-align: right;
    }

    .form-buttons input[type="submit"] {
        padding: 8px 16px;
        background: #a17c61;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .form-buttons input[type="submit"]:hover {
        background: #8c6750;
    }
    </style>
</head>
<body>
<div class="container form-page">
    <h2>Edit Booking</h2>
    <form method="POST" action="">
        <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">

        <div class="form-row">
            <div class="form-group half">
                <label>Facility:</label>
                <select name="facility_id" required>
                    <?php while($f = $facilities->fetch_assoc()): ?>
                        <option value="<?= $f['facility_id'] ?>" <?= $f['facility_id'] == $booking['facility_id'] ? 'selected' : '' ?>>
                            <?= $f['name'] . ' - ' . $f['location'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group half">
                <label>Time In:</label>
                <input type="time" name="time_in" value="<?= $booking['time_in'] ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group half">
                <label>Start Date:</label>
                <input type="date" name="start_date" value="<?= $booking['start_date'] ?>" required>
            </div>

            <div class="form-group half">
                <label>Time Out:</label>
                <input type="time" name="time_out" value="<?= $booking['time_out'] ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group half">
                <label>End Date:</label>
                <input type="date" name="end_date" value="<?= $booking['end_date'] ?>">
            </div>

            <div class="form-group half">
                <label>Booked By:</label>
                <input type="text" name="booked_by" value="<?= $booking['booked_by'] ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group full">
                <label>Description:</label>
                <textarea name="description" rows="4" required><?= $booking['description'] ?></textarea>
            </div>
        </div>

        <div class="form-buttons">
            <input type="submit" value="Update Booking">
            <a href="index.php" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if ($showAccessDenied): ?>
<script>
    Swal.fire({
        icon: "error",
        title: "Access Denied",
        text: "Access denied. Only Admin or Staff can access this page.",
        confirmButtonColor: "#d33"
    }).then(() => {
        window.location.href = "index.php";
    });
</script>
<?php endif; ?>

</body>
</html>
