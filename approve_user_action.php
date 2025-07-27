<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $update = "UPDATE users SET status='approved' WHERE id=$id";
    if ($conn->query($update) === TRUE) {
        echo 'success';
    } else {
        echo 'error';
    }
} else {
    echo 'invalid';
}
?>
