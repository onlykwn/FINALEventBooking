<?php include 'db.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup</title>
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

        input,
        select {
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

        input:focus,
        select:focus {
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
        <h2>SIGN UP</h2>

        <?php
        $showToast = false;
        $toastMessage = '';
        $toastIcon = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username']);
            $password = $_POST['password'];
            $role = $_POST['role'];

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Check if username exists
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $toastMessage = "Username already taken.";
                $toastIcon = "error";
                $showToast = true;
            } else {
                $status = ($role === 'staff') ? 'pending' : 'approved';

                $stmt = $conn->prepare("INSERT INTO users (username, password, role, status) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $username, $hashedPassword, $role, $status);
                $stmt->execute();

                if ($role === 'staff') {
                    $toastMessage = "Your account has been created. Please wait for admin approval.";
                    $toastIcon = "info";
                } else {
                    $toastMessage = "Your account has been created successfully. You can now log in.";
                    $toastIcon = "success";
                }

                $showToast = true;
            }
        }
        ?>

        <form method="post">
            <input type="text" name="username" placeholder="Choose Username" required>
            <input type="password" name="password" placeholder="Choose Password" required>
            <select name="role" required>
                <option value="student">Student</option>
                <option value="staff">Staff</option>
            </select>
            <button type="submit">Create Account</button>
        </form>

        <a href="login.php">← Back to Login</a>
    </div>

    <?php if ($showToast): ?>
    <script>
        Swal.fire({
            toast: true,
            position: 'top', // centered at top
            icon: '<?php echo $toastIcon; ?>',
            title: '<?php echo $toastMessage; ?>',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true
        });
    </script>
    <?php endif; ?>
</body>
</html>
