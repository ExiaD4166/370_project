<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ticket_booking";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['coach_id'], $_POST['route_id'], $_POST['fare'], $_POST['selected_seats'])) {
        die("Missing booking details. Please try again.");
    }

    $username = $_SESSION['username'];
    $coach_id = intval($_POST['coach_id']);
    $route_id = intval($_POST['route_id']);
    $fare = intval($_POST['fare']);
    $selected_seats = $_POST['selected_seats'];
    $journey_date = date("Y-m-d");

    // Begin transaction
    $conn->begin_transaction();

    try {
        $insertSql = "INSERT INTO book (username, coach_id, seat_id, journey_date) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insertSql);

        foreach ($selected_seats as $seat_id) {
            $stmt->bind_param("siss", $username, $coach_id, $seat_id, $journey_date);
            if (!$stmt->execute()) {
                throw new Exception("Error inserting booking: " . $stmt->error);
            }

            // Mark the seat as booked
            $updateSql = "UPDATE seats SET seat_status = 'booked' WHERE coach_id = ? AND seat_id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("is", $coach_id, $seat_id);
            if (!$updateStmt->execute()) {
                throw new Exception("Error updating seat status: " . $updateStmt->error);
            }
        }

        $conn->commit();

        echo "<h1>Booking Confirmed!</h1>";
        echo "<p>Seats Booked: " . implode(", ", $selected_seats) . "</p>";
        echo "<p>Total Fare: " . ($fare * count($selected_seats)) . "</p>";
    } catch (Exception $e) {
        $conn->rollback();
        die("Error: " . $e->getMessage());
    }

    $stmt->close();
    $conn->close();
} else {
    die("Invalid request method.");
}
?>