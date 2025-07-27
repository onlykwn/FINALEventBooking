<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $sql = "UPDATE users SET status = 'declined' WHERE id = $id";
    
    if ($conn->query($sql) === TRUE) {
        echo "success";
    } else {
        echo "error";
    }
}
?>
