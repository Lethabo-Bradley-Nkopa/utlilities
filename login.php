<?php
include 'connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        $sql = "SELECT `pswd` FROM `chat` WHERE email=? LIMIT 1;";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                session_regenerate_id(true);
                $_SESSION["email"] = $email;
                header("Location: home.php");
                exit();
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Invalid email or password.";
        }

        $stmt->close();
    } else {
        $error = "All fields are required!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Chat App</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background: linear-gradient(to right, #141e30, #243b55);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            width: 350px;
            text-align: center;
        }

        .login-container h2 {
            color: #fff;
            margin-bottom: 20px;
            font-size: 24px;
        }

        .login-container label {
            display: block;
            text-align: left;
            color: #ddd;
            font-size: 14px;
            margin: 10px 0 5px;
        }

        .login-container input {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 16px;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .login-container input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .login-container input:focus {
            outline: none;
            border: 2px solid #00c6ff;
        }

        .login-container button {
            width: 100%;
            padding: 10px;
            background: #00c6ff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            color: #fff;
            cursor: pointer;
            transition: 0.3s;
        }

        .login-container button:hover {
            background: #0072ff;
        }

        .error {
            color: #ff4d4d;
            font-size: 14px;
            margin-top: 10px;
        }

        .signup-link {
            color: #ddd;
            text-decoration: none;
            display: block;
            margin-top: 15px;
        }

        .signup-link:hover {
            color: #00c6ff;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="email">Email</label>
            <input type="email" name="email" placeholder="Enter your email" required>

            <label for="password">Password</label>
            <input type="password" name="password" placeholder="Enter your password" required>

            <button type="submit">Login</button>
        </form>
        <?php if (!empty($error)) { echo "<p class='error'>$error</p>"; } ?>
        <a href="http://localhost/chat/signup.php" class="signup-link">Don't have an account? Sign up</a>
    </div>
</body>
</html>