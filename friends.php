<?php
include 'connection.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION["email"];

// Fetch all users except the logged-in user
$sql = "SELECT user_id, first_name, last_name, profile_pic FROM `chat` WHERE email != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friends List</title>
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

        .container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 400px;
        }

        .friend {
            margin: 15px 0;
            padding: 10px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }

        .friend img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 5px;
            border: 2px solid #00c6ff;
        }

        .friend a {
            text-decoration: none;
            color: white;
            font-weight: bold;
            transition: 0.3s;
        }

        .friend a:hover {
            color: #00c6ff;
        }

        .logout {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            padding: 10px 15px;
            background: #00c6ff;
            color: white;
            border-radius: 5px;
            transition: 0.3s;
        }

        .logout:hover {
            background: #0072ff;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Your Friends</h2>
    <?php while ($row = $result->fetch_assoc()) { ?>
        <div class="friend">
            <img src="profile_pics/<?php echo htmlspecialchars($row['profile_pic']); ?>" alt="Profile Picture">
            <br>
            <a href="profile.php?id=<?php echo $row['user_id']; ?>">
                <?php echo htmlspecialchars($row['first_name'] . " " . $row['last_name']); ?>
            </a>
        </div>
    <?php } ?>
    
    <a href="logout.php" class="logout">Log out</a>
</div>

</body>
</html>
