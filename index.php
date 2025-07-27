<?php 
session_start();
include 'db.php'; 

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$showLoginAlert = false;
if (isset($_SESSION['login_success'])) {
    $showLoginAlert = true;
    unset($_SESSION['login_success']);
}

echo "<div style='text-align:right; padding: 10px; font-weight: bold; font-family: Arial;'>
Logged in as: <span style='color:#8b2f33'>" . htmlspecialchars($_SESSION['user']) . " (" . ucfirst($_SESSION['role']) . ")</span>
</div>";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Booking List</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="style.css">
<style>
        .btn {
            color: #912d2b;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.2s ease;
        }
        .status-booked {
            color: #912d2b;
            font-weight: bold;
        }
        .button-action a {
            margin-right: 6px;
            color: #7c2e2e;
        }
        .action-buttons {
            display: flex;
            gap: 6px;
        }
        
    </style>
</head>
<body>
<div class="container home-page">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2 style="color: #7c2e2e;">FACILITY BOOKING LIST</h2>
        <div>
            <?php if ($_SESSION['role'] === 'admin') {
                $pending_count_query = $conn->query("SELECT COUNT(*) AS pending_total FROM users WHERE status = 'pending'");
                $pending = $pending_count_query->fetch_assoc()['pending_total'];
            ?>
                <a href="approve_users.php" class="btn">Approved Accounts<?php if ($pending > 0) echo " ($pending)"; ?></a>
            <?php } ?>
            <?php if ($_SESSION['role'] !== 'student') { ?>
                <a href="add_booking.php" class="btn">+ Add New Booking</a>
            <?php } ?>
            <a href="logout.php" class="btn">Logout</a>
        </div>
    </div>

    <br>

    <table border="1" cellpadding="8">
        <tr>
            <th>Facility</th>
            <th>Date</th>
            <th>Time</th>
            <th>Booked By</th>
            <th>Description</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php
        $sql = "SELECT 
                    f.name AS facility, 
                    f.location,
                    b.booking_id,
                    b.start_date, 
                    b.end_date, 
                    b.time_in, 
                    b.time_out, 
                    b.booked_by, 
                    b.description, 
                    b.status 
                FROM bookings b 
                LEFT JOIN facilities f ON b.facility_id = f.facility_id
                WHERE b.status = 'Booked'
                ORDER BY b.start_date ASC";

        $result = $conn->query($sql);
        if (!$result) {
            die("SQL Error: " . $conn->error);
        }

        while ($row = $result->fetch_assoc()) {
            $status_class = 'status-booked';

            $start = new DateTime($row['start_date']);
            $end = isset($row['end_date']) && $row['end_date'] !== null ? new DateTime($row['end_date']) : clone $start;
            $end = $end->modify('+1 day');

            $interval = new DateInterval('P1D');
            $date_range = new DatePeriod($start, $interval, $end);

            foreach ($date_range as $date) {
                $formatted_date = $date->format("Y-m-d");

                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['facility'] ?? 'N/A') . " - " . htmlspecialchars($row['location'] ?? 'N/A') . "</td>";
                echo "<td>$formatted_date</td>";
                echo "<td>" . date("g:i A", strtotime($row['time_in'])) . " - " . date("g:i A", strtotime($row['time_out'])) . "</td>";
                echo "<td>" . htmlspecialchars($row['booked_by']) . "</td>";
                echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                echo "<td class='$status_class'>" . htmlspecialchars($row['status']) . "</td>";
                echo "<td class='button-action'>";
                echo '<div class="action-buttons">';
                if ($_SESSION['role'] !== 'student') {
                    echo '<a href="edit_booking.php?id=' . $row['booking_id'] . '">Edit</a>';
                    echo '<a href="mark_available.php?id=' . $row['booking_id'] . '">Mark Available</a>';
                } else {
                    echo "<em>N/A</em>";
                }
                echo "</div></td></tr>";
            }   
        }
        
        ?>
    </table>
<h2 style="color: #7c2e2e; text-align: left;">AVAILABLE BOOKINGS</h2><br>

<style>
/* MATCHED STYLES */
table {
  width: 100%;
  border-collapse: collapse;
  table-layout: auto;
  word-wrap: break-word;
}
table th {
  text-transform: uppercase;
  text-align: center;
  font-weight: bold;
  background-color: #f3cccc;
  color: #5c1d1d;
}
table td {
  text-align: center;
  text-transform: none;
  word-break: break-word;
  white-space: normal;
  max-width: 300px;
}
th, td {
  padding: 12px;
  border: 1px solid #e6c2c2;
}
</style>

<table>
  <tr>
    <th>Facility</th>
    <th>Date</th>
    <th>Time</th>
    <th>Booked By</th>
    <th>Description</th>
    <th>Status</th>
    <th>Action</th>
  </tr>

<?php
$sql = "SELECT
    f.name AS facility,
    f.location,
    b.booking_id,
    b.start_date,
    b.end_date,
    b.time_in,
    b.time_out,
    b.booked_by,
    b.description,
    b.status
  FROM bookings b
  LEFT JOIN facilities f ON b.facility_id = f.facility_id
  WHERE b.status = 'Available'
  ORDER BY b.start_date ASC";

$result = $conn->query($sql);
if (!$result) {
  die("SQL Error: " . $conn->error);
}

while ($row = $result->fetch_assoc()) {
  $status_class = 'status-available';

  $start = new DateTime($row['start_date']);
  $end = isset($row['end_date']) && $row['end_date'] !== null ? new DateTime($row['end_date']) : clone $start;
  $end = $end->modify('+1 day');

  $interval = new DateInterval('P1D');
  $date_range = new DatePeriod($start, $interval, $end);

  foreach ($date_range as $date) {
    $formatted_date = $date->format("Y-m-d");

    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['facility'] ?? 'N/A') . " - " . htmlspecialchars($row['location'] ?? 'N/A') . "</td>";
    echo "<td>$formatted_date</td>";
    echo "<td>" . date("g:i A", strtotime($row['time_in'])) . " - " . date("g:i A", strtotime($row['time_out'])) . "</td>";
    echo "<td>" . htmlspecialchars($row['booked_by']) . "</td>";
    echo "<td>" . htmlspecialchars($row['description']) . "</td>";
    echo "<td class='$status_class'>" . htmlspecialchars($row['status']) . "</td>";
    echo "<td class='button-action'>";
    echo '<div class="action-buttons">';
    echo '<a href="edit_booking.php?id=' . $row['booking_id'] . '">Edit</a> ';
    echo '<a href="javascript:void(0);" onclick="confirmDelete(' . $row['booking_id'] . ')">Delete</a>';
    echo "</div></td></tr>";
  }
}
?>
</table>


</div>

<?php if (isset($_SESSION['swal'])): ?>
<script>
Swal.fire({
    icon: '<?php echo $_SESSION["swal"]["icon"]; ?>',
    title: '<?php echo $_SESSION["swal"]["title"]; ?>',
    text: '<?php echo $_SESSION["swal"]["text"]; ?>',
    confirmButtonColor: '#912d2b'
});
</script>
<?php unset($_SESSION['swal']); ?>
<?php endif; ?>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: "Are you sure?",
        text: "This will permanently delete the booking.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#b03d3d",
        cancelButtonColor: "#aaa",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "delete_booking.php?id=" + id;
        }
    });
}
</script>
<?php if (isset($_SESSION['swal'])): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Swal.fire({
    toast: true,
    position: 'top',
    icon: '<?php echo $_SESSION['swal']['icon']; ?>',
    title: '<?php echo $_SESSION['swal']['title']; ?>',
    text: '<?php echo $_SESSION['swal']['text']; ?>',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.style.top = '20px';
    },
    showClass: {
        popup: 'swal2-slide-in-top'
    },
    hideClass: {
        popup: 'swal2-slide-out-top'
    }
});
</script>
<?php unset($_SESSION['swal']); endif; ?>

</body>
</html>
