<?php
session_start();

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: signup.html?message=Please login first.");
    exit;
}

if (!isset($_POST['coach_id'], $_POST['route_id'], $_POST['fare'], $_POST['seat_ids'], $_POST['journey_date'])) {
    die("Missing required parameters.");
}

$username = $_SESSION['username'];
$coach_id = $_POST['coach_id'];
$route_id = $_POST['route_id'];
$fare = $_POST['fare'];
$seat_ids = explode(",", $_POST['seat_ids']);
$journey_date = $_POST['journey_date'];

$servername = "localhost";
$username_db = "root";
$password = "";
$dbname = "ticket_booking";

$conn = new mysqli($servername, $username_db, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

foreach ($seat_ids as $seat_id) {
    $sql = "INSERT INTO book (username, coach_id, seat_id, route_id, journey_date) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $username, $coach_id, $seat_id, $route_id, $journey_date);
    if (!$stmt->execute()) {
        die("Error booking seat: " . $stmt->error);
    }
    $stmt->close();
}

$conn->close();
header("Location: booking_summary.php?date=$journey_date");
exit;
?>
