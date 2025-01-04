<?php
session_start(); // Start the session

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ticket_booking";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($email === 'admin@email.com' && $password === 'admin123') {
        // Admin login
        $_SESSION['logged_in'] = true;
        $_SESSION['is_admin'] = true;
        header("Location: admin_dashboard.php");
        exit;
    }

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['username'];
            $_SESSION['is_admin'] = false;

            // Redirect to search page
            header("Location: search.php");
            exit;
        } else {
            header("Location: signup.html?message=Invalid password.&type=error");
        }
    } else {
        header("Location: signup.html?message=User not found.&type=error");
    }
}

$conn->close();
?>
