<?php
include 'connection.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION["email"];

// Fetch user data securely
$sql = "SELECT * FROM `chat` WHERE email = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// If user doesn't exist, log them out
if (!$user) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// Store additional session data
$_SESSION["id"] = $user["user_id"];
$_SESSION["name"] = $user["first_name"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Chat App</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(to right, #141e30, #243b55);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #fff;
        }

        .profile-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 400px;
        }

        .profile-container h1 {
            margin-bottom: 10px;
            font-size: 22px;
        }

        .profile-container img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-top: 10px;
            border: 3px solid #00c6ff;
        }

        .profile-container a {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            padding: 10px 15px;
            background: #00c6ff;
            color: white;
            border-radius: 5px;
            transition: 0.3s;
        }

        .profile-container a:hover {
            background: #0072ff;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <h1>Welcome, <?php echo htmlspecialchars($user["first_name"] . " " . $user["last_name"]); ?>!</h1>
        <h2>Email: <?php echo htmlspecialchars($user["email"]); ?></h2>
        <img src="profile_pics/<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Profile Picture">
        <br><br>
        <a href="friends.php">Friends</a>
        <a href="logout.php">Log out</a>
    </div>
</body>
</html>
