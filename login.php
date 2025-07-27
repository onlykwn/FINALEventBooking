<?php
session_start();
include 'db.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
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

        .login-box {
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

        input[type="text"],
        input[type="password"] {
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
    </style>
</head>
<body>
    <div class="login-box">
        <h2>LOGIN</h2>

        <?php
        function showToast($icon, $title) {
            echo "<script>
                Swal.fire({
                    toast: true,
                    position: 'top',
                    icon: '$icon',
                    title: '$title',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true,
                    showClass: {
                        popup: 'swal2-show slideInDown'
                    },
                    customClass: {
                        popup: 'animated-toast'
                    }
                });
            </script>";
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            if ($username === 'Admin' && $password === 'ADMIN12345') {
                $_SESSION['user'] = 'Admin';
                $_SESSION['role'] = 'admin';
                $_SESSION['user_id'] = 0;
                $_SESSION['login_success'] = true;
                header("Location: index.php");
                exit();
            }

            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();

                if (password_verify($password, $user['password'])) {
                    if ($user['status'] === 'pending') {
                    showToast('info', 'Your account is pending for approval by the admin');
                    } elseif ($user['status'] === 'declined') {
                    showToast('error', 'This account has been declined by the admin');
                    } elseif ($user['status'] === 'approved') {

                        $_SESSION['user'] = $user['username'];
                        $_SESSION['role'] = $user['role'];
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['login_success'] = true;
                        header("Location: index.php");
                        exit();
                    }
                } else {
                    showToast('warning', 'Invalid password');
                }
            } else {
                showToast('warning', 'User not found');
            }
        }
        ?>

        <form method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <a href="signup.php">Don't have an account? Sign up</a>
        <a href="forgot_password.php">Forgot your password?</a>
    </div>

    <script>
        // Optional animation styles for slide in down
        const style = document.createElement('style');
        style.innerHTML = `
            .swal2-show.slideInDown {
                animation: slideInDown 0.5s ease;
            }

            @keyframes slideInDown {
                from {
                    transform: translateY(-2rem);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
