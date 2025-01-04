<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ticket_booking";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['booking_id'], $_GET['coach_id'], $_GET['seat_id'], $_GET['fare'], $_GET['journey_date'])) {
    $booking_id = $_GET['booking_id'];
    $coach_id = $_GET['coach_id'];
    $seat_id = $_GET['seat_id'];
    $fare = $_GET['fare'];
    $journey_date = $_GET['journey_date'];
    $username = $_SESSION['username']; // Ensure user is logged in

    echo "<h2>Booking Confirmed</h2>";
    echo "<p><strong>Booking ID:</strong> $booking_id</p>";
    echo "<p><strong>Coach ID:</strong> $coach_id</p>";
    echo "<p><strong>Seat ID:</strong> $seat_id</p>";
    echo "<p><strong>Fare:</strong> $fare</p>";
    echo "<p><strong>Journey Date:</strong> $journey_date</p>";
} else {
    echo "Invalid request.";
}

$conn->close();
?>