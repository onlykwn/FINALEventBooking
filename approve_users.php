<?php
include 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Display logged in role
echo "<div style='text-align:right; padding: 10px; font-weight: bold; font-family: Arial;'>
Logged in as: <span style='color:#8b2f33'>" . ucfirst($_SESSION['role']) . "</span>
</div>";

?>

<!DOCTYPE html>
<html>
<head>
    <title>Approve Users</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
table {
  width: 100%;
  border-collapse: collapse;
  background: #fffaf4;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  table-layout: fixed; /* <-- forces equal column width */
  word-wrap: break-word;
}

table th, table td {
  text-align: center;
  padding: 12px;
  border: 1px solid #e6c2c2;
  word-break: break-word;
}

table th {
  text-transform: uppercase;
  font-weight: bold;
  background-color: #f3cccc;
  color: #5c1d1d;
}

table td {
  text-transform: none;
  white-space: normal;
  max-width: 300px;
}

/* Action Buttons Container */
.action-buttons {
  display: flex;
  justify-content: center;
  gap: 8px;
}

/* Green Approve Button */
.btn-approve {
  background-color: #5cb85c;
  color: white;
  border: none;
  padding: 6px 8px;
  border-radius: 5px;
  cursor: pointer;
  font-size: 14px;
  width: 80px; /* Fits inside cell better */
}
.btn-approve:hover {
  background-color: #449d44;
}

/* Red Decline Button */
.btn-decline {
  background-color: #d9534f;
  color: white;
  border: none;
  padding: 6px 8px;
  border-radius: 5px;
  cursor: pointer;
  font-size: 14px;
  width: 80px; /* Same size as approve */
}
.btn-decline:hover {
  background-color: #c9302c;
}

/* Container */
.container.home-page {
  display: block;
  width: 60%;
  padding: 20px;
  background-color: #fff2f2;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  margin: 20px auto;
}

</style>

</head>
<body>

<div class="container home-page">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2 style="color: #7c2e2e;">PENDING USER APPROVALS</h2>
        <div>
            <a href="index.php" class="btn">&larr; Back to  Dashboard</a>
        </div>
    </div>

    <br>

    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Role</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT id, username, role, status FROM users WHERE status = 'pending'"; // or add AND role = 'staff'
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
                $userId = htmlspecialchars($row['id']);
                $username = htmlspecialchars($row['username']);
                $role = htmlspecialchars($row['role']);
                $status = htmlspecialchars($row['status']);
        ?>
            <tr id="user-<?php echo $userId; ?>">
                <td><?php echo $username; ?></td>
                <td><?php echo $role; ?></td>
                <td><?php echo $status; ?></td>
             <td>
  <div class="action-buttons">
<button class="btn-approve" onclick="approveUser(<?php echo $userId; ?>)">Approve</button>
<button class="btn-approve" style="background-color: #c9302c;" onclick="declineUser(<?php echo $userId; ?>)">Decline</button>
</div>
</td>

</td>


            </tr>
        <?php
            endwhile;
        else:
        ?>
            <tr><td colspan="4">No users pending approval.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function approveUser(userId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "Approve this user?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#a94442',
        cancelButtonColor: '#bbb',
        confirmButtonText: 'Yes, approve'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('approve_user_action.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'id=' + userId
            })
            .then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    document.getElementById('user-' + userId).remove();
                    Swal.fire('Approved!', 'User has been approved.', 'success');
                } else {
                    Swal.fire('Error!', 'Could not approve user.', 'error');
                }
            });
        }
    });
}
</script>
<script>
function declineUser(userId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "Decline this user? They will not be able to log in.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#999',
        cancelButtonColor: '#bbb',
        confirmButtonText: 'Yes, decline'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('decline_user_action.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'id=' + userId
            })
            .then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    document.getElementById('user-' + userId).remove();
                    Swal.fire('Declined!', 'User has been declined.', 'success');
                } else {
                    Swal.fire('Error!', 'Could not decline user.', 'error');
                }
            });
        }
    });
}
</script>

</body>
</html>
