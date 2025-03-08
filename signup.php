<?php
include 'connection.php';

session_start();
$error = "";

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $fname = trim($_POST["fname"]);
    $lname = trim($_POST["lname"]);
    $dob = $_POST["dob"];
    $gender = trim($_POST["gender"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $cpassword = $_POST["cpassword"];
    $device = $_SERVER["HTTP_USER_AGENT"];
    
    // Validate required fields
    if (empty($fname) || empty($lname) || empty($dob) || empty($gender) || empty($email) || empty($password) || empty($cpassword) || empty($_FILES["pic"]["name"])) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $cpassword) {
        $error = "Passwords do not match.";
    } else {
        // File Upload Handling
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $pic = $_FILES["pic"];
        $fileType = mime_content_type($pic["tmp_name"]);
        
        if (!in_array($fileType, $allowedTypes)) {
            $error = "Invalid file type. Please upload a valid image.";
        } elseif ($pic['size'] > 2 * 1024 * 1024) { // Limit to 2MB
            $error = "File size exceeds 2MB limit.";
        } else {
            // Generate unique filename
            $milliseconds = round(microtime(true) * 1000);
            $newFileName = $milliseconds . "." . pathinfo($pic["name"], PATHINFO_EXTENSION);
            
            move_uploaded_file($pic['tmp_name'], "profile_pics/" . $newFileName);

            // Check if email already exists
            $check_email = $conn->prepare("SELECT email FROM chat WHERE email = ? LIMIT 1;");
            $check_email->bind_param('s', $email);
            $check_email->execute();
            $check_email->store_result();
            
            if ($check_email->num_rows > 0) {
                $error = "Email is already taken.";
            } else {
                // Insert user into database
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO chat (first_name, last_name, date_of_birth, gender, profile_pic, email, pswd, date, device) VALUES (?, ?, ?, ?, ?, ?, ?, current_time(), ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ssssssss', $fname, $lname, $dob, $gender, $newFileName, $email, $hashed_password, $device);
                
                if ($stmt->execute()) {
                    header("Location: login.php");
                    exit();
                } else {
                    $error = "Registration failed. Try again.";
                }
            }
            $check_email->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | Chat App</title>
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
        }

        .signup-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
        }

        .signup-container h2 {
            color: #fff;
            margin-bottom: 20px;
            font-size: 24px;
        }

        .signup-container label {
            display: block;
            text-align: left;
            color: #ddd;
            font-size: 14px;
            margin: 10px 0 5px;
        }

        .signup-container input {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 16px;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .signup-container input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .signup-container input:focus {
            outline: none;
            border: 2px solid #00c6ff;
        }

        .signup-container button {
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

        .signup-container button:hover {
            background: #0072ff;
        }

        .error {
            color: #ff4d4d;
            font-size: 14px;
            margin-top: 10px;
        }

        .login-link {
            color: #ddd;
            text-decoration: none;
            display: block;
            margin-top: 15px;
        }

        .login-link:hover {
            color: #00c6ff;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2>Sign Up</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <label>First Name</label>
            <input type="text" name="fname" placeholder="Enter your first name" required>

            <label>Last Name</label>
            <input type="text" name="lname" placeholder="Enter your last name" required>

            <label>Gender</label>
            <input type="text" name="gender" placeholder="Male/Female/Other" required>

            <label>Upload Profile Picture</label>
            <input type="file" name="pic" accept="image/*" required>

            <label>Date of Birth</label>
            <input type="date" name="dob" max="2015-12-31" min="1925-12-31" required>

            <label>Email</label>
            <input type="email" name="email" placeholder="Enter your email" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Create a password" required>

            <label>Confirm Password</label>
            <input type="password" name="cpassword" placeholder="Re-enter your password" required>

            <button type="submit">Sign Up</button>
        </form>
        <?php if (!empty($error)) { echo "<p class='error'>$error</p>"; } ?>
        <a href="login.php" class="login-link">Already have an account? Log in here</a>
    </div>
</body>
</html>
