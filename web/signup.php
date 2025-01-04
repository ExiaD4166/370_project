<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ticket_booking";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $phone = $_POST['phone'];

    $sql = "INSERT INTO users (username, email, password, phone) VALUES ('$username', '$email', '$password', '$phone')";

    if ($conn->query($sql) === TRUE) {
        header("Location: signup.html?message=Signup successful! Now you can login.&type=success");
        exit;
    } else {
        if ($conn->errno === 1062) {
            header("Location: signup.html?message=Username or email already exists.&type=error");
        } else {
            header("Location: signup.html?message=Error: " . $conn->error . "&type=error");
        }
        exit;
    }
}

$conn->close();
?>
