<?php include 'db.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #fbecec;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .box {
            width: 350px;
            padding: 30px;
            background: #fff2f2;
            border-left: 6px solid #b05b5b;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        h2 {
            text-align: center;
            color: #741b1b;
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 12px 0;
            border: 1px solid #c99797;
            border-radius: 6px;
            background: #fffafa;
            box-sizing: border-box;
            font-size: 14px;
            transition: border 0.3s ease;
        }

        input:focus {
            border-color: #a94442;
            outline: none;
            box-shadow: 0 0 4px rgba(169, 68, 66, 0.2);
        }

        button {
            width: 100%;
            padding: 10px;
            background: #a94442;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #822626;
        }

        a {
            text-decoration: none;
            color: #992e2e;
            font-size: 13px;
            display: block;
            text-align: center;
            margin-top: 10px;
            transition: color 0.2s ease;
        }

        a:hover {
            color: #6b1414;
            text-decoration: underline;
        }

        p {
            text-align: center;
            font-size: 14px;
            color: #c0392b;
            margin-bottom: 0;
        }

        p a {
            color: #a94442;
            font-weight: bold;
        }

        p a:hover {
            color: #6b1414;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="box">
        <h2>RESET PASSWORD</h2>

        <?php
        $message = "";
        $type = "";

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $newpass = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    $check = $conn->query("SELECT * FROM users WHERE username = '$username'");
    if ($check->num_rows > 0) {
        $conn->query("UPDATE users SET password = '$newpass' WHERE username = '$username'");
        $message = "Password successfully updated!";
        $type = "success";
    } else {
        $message = "Username not found.";
        $type = "error";
    }
}
        ?>

        <form method="post">
            <input type="text" name="username" placeholder="Your Username" required>
            <input type="password" name="new_password" placeholder="New Password" required>
            <button type="submit">Reset Password</button>
        </form>

        <a href="login.php">← Back to Login</a>
    </div>

    <?php if (!empty($message)): ?>
    <script>
        Swal.fire({
            toast: true,
            position: 'top',
            icon: '<?php echo $type; ?>',
            title: '<?php echo $message; ?>',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.style.animation = 'swal2-toast-show 0.6s ease-out';
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>
