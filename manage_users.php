<?php include 'db.php'; 

if ($_SESSION['role'] !== 'admin') {
    echo "Access denied. Admin only.";
    exit();
}
