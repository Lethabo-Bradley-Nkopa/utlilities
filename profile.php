<?php
include 'connection.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION["id"];

// Validate user ID from URL
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Invalid profile ID.");
}

$user_id = intval($_GET["id"]); // Ensure it's an integer

// Prevent viewing own profile
if ($id == $user_id) {
    header("Location: home.php");
    exit();
}

// Fetch profile data
$sql = "SELECT first_name, last_name, email, profile_pic FROM `chat` WHERE user_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if user exists
if ($result->num_rows == 0) {
    die("User not found.");
}

$row = $result->fetch_assoc();
$tname = $row["first_name"];
$lname = $row["last_name"];
$email = $row["email"];
$profile_pic = $row["profile_pic"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($tname . " " . $lname); ?></title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(to right, #1c1c1c, #444);
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

        .profile-img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 10px;
    border: 3px solid #00c6ff;
    display: block;
    margin-left: auto;
    margin-right: auto;
}


        .btn {
            display: inline-block;
            margin: 10px 0;
            padding: 10px 15px;
            text-decoration: none;
            background: #00c6ff;
            color: white;
            border-radius: 5px;
            transition: 0.3s;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background: #0072ff;
        }
    </style>
</head>
<body>

<div class="container">
    <h2><?php echo htmlspecialchars($tname . " " . $lname); ?></h2>
    <p>Email: <?php echo htmlspecialchars($email); ?></p>
    
    <a href="profile_pics/<?php echo htmlspecialchars($profile_pic); ?>" target="_blank">
        <img src="profile_pics/<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture" class="profile-img">
    </a>
    
    <a href="message.php?id=<?php echo $user_id; ?>" class="btn">Message <?php echo htmlspecialchars($tname); ?></a>
    <br>
    <a href="logout.php" class="btn">Log out</a>
</div>

</body>
</html>
