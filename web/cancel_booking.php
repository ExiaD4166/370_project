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

// Check if required data is received
if (!isset($_POST['booking_id'], $_POST['coach_id'], $_POST['seat_id'])) {
    die("Invalid request.");
}

$booking_id = intval($_POST['booking_id']);
$coach_id = intval($_POST['coach_id']);
$seat_id = $_POST['seat_id'];

// Begin transaction
$conn->begin_transaction();

try {
    // Delete the booking
    $sql = "DELETE FROM book WHERE booking_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();

    // Mark the seat as available
    $sql = "UPDATE seats SET seat_status = 'available' WHERE coach_id = ? AND seat_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $coach_id, $seat_id);
    $stmt->execute();

    // Commit transaction
    $conn->commit();

    header("Location: my_bookings.php?message=Booking canceled successfully.");
} catch (Exception $e) {
    $conn->rollback();
    die("Error: " . $e->getMessage());
}

$stmt->close();
$conn->close();
?>
