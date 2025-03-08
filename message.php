<?php
include 'connection.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}

$sender_id = $_SESSION["id"];
$receiver_id = isset($_GET["id"]) ? intval($_GET["id"]) : die("Invalid user ID.");

// Fetch receiver's details
$sql = "SELECT first_name, last_name FROM `chat` WHERE user_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $receiver_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("User not found.");
}

$receiver = $result->fetch_assoc();
$receiver_name = $receiver["first_name"] . " " . $receiver["last_name"];

// Handle message submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["message"])) {
    $message = htmlspecialchars($_POST["message"]);
    if (!empty($message)) {
        // Fetch the sender's email
        $sql = "SELECT email FROM chat WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $sender_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $sender = $result->fetch_assoc();
        $sender_email = $sender['email'];

        // Fetch the receiver's email
        $sql = "SELECT email FROM chat WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $receiver_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $receiver = $result->fetch_assoc();
        $receiver_email = $receiver['email'];

        // Insert the message into the messages table
        $sql = "INSERT INTO messages (sender_id, reciever_id, sender_email, reciver_email, message, time) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issss", $sender_id, $receiver_id, $sender_email, $receiver_email, $message);
        $stmt->execute();
    }
}

// Fetch messages from the messages table
$sql = "SELECT m.message, m.time, u.first_name, u.last_name FROM messages m
        JOIN chat u ON (m.sender_id = u.user_id) 
        WHERE (m.sender_id = ? AND m.reciever_id = ?) OR (m.sender_id = ? AND m.reciever_id = ?)
        ORDER BY m.time ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
$stmt->execute();
$messages = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message with <?php echo htmlspecialchars($receiver_name); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(to right, #1c1c1c, #444);
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            height: 100vh;
            padding: 20px;
        }

        .chat-container {
            width: 100%;
            max-width: 500px;
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            margin-bottom: 20px;
        }

        .chat-header {
            font-size: 1.5rem;
            text-align: center;
            margin-bottom: 20px;
        }

        .message-box {
            flex-grow: 1;
            overflow-y: auto;
            margin-bottom: 20px;
            padding-right: 10px;
            display: flex;
            flex-direction: column;
        }

        .message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 10px;
            max-width: 80%;
            word-wrap: break-word;
        }

        .message.sent {
            background-color: #00c6ff;
            margin-left: auto;
            text-align: right;
            align-self: flex-end;
        }

        .message.received {
            background-color: #333;
            margin-right: auto;
            text-align: left;
            align-self: flex-start;
        }

        .input-area {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .input-area textarea {
            width: 80%;
            padding: 10px;
            border-radius: 5px;
            border: none;
            resize: none;
            background-color: #222;
            color: #fff;
        }

        .input-area button {
            padding: 10px 20px;
            background: #00c6ff;
            color: white;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }

        .input-area button:hover {
            background: #0072ff;
        }

        .logout-link {
            position: absolute;
            bottom: 20px;
            text-align: center;
            width: 100%;
        }

        .logout-link a {
            color: #00c6ff;
            text-decoration: none;
            font-weight: bold;
        }

        .logout-link a:hover {
            color: #0072ff;
        }
    </style>
</head>
<body>

<div class="chat-container">
    <div class="chat-header">
        Chat with <?php echo htmlspecialchars($receiver_name); ?>
    </div>

    <div class="message-box">
        <?php while ($msg = $messages->fetch_assoc()): ?>
            <div class="message <?php echo ($msg['first_name'] == 'You') ? 'sent' : 'received'; ?>">
                <strong><?php echo htmlspecialchars($msg['first_name'] . ' ' . $msg['last_name']); ?>:</strong>
                <p><?php echo htmlspecialchars($msg['message']); ?></p>
                <small><?php echo date("h:i A", strtotime($msg['time'])); ?></small>
            </div>
        <?php endwhile; ?>
    </div>

    <form method="POST" class="input-area">
        <textarea name="message" rows="3" placeholder="Type a message..."></textarea>
        <button type="submit">Send</button>
    </form>
</div>

<div class="logout-link">
    <a href="logout.php">Log out</a>
</div>

</body>
</html>
