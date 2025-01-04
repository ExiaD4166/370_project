<?php
session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: signup.html?message=Unauthorized access.&type=error");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ticket_booking";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle new bus addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_bus'])) {
    $route_id = $_POST['route_id'];
    $coach_id = $_POST['coach_id'];
    $departure_time = $_POST['departure_time'];
    $bus_type = 'Non AC';
    $total_seats = 36;

    $add_bus_query = "INSERT INTO Bus (coach_id, route_id, bus_type, departure_time, total_seats) VALUES ('$coach_id', '$route_id', '$bus_type', '$departure_time', '$total_seats')";
    if ($conn->query($add_bus_query) === TRUE) {
        for ($i = 0; $i < 9; $i++) {
            for ($j = 1; $j <= 4; $j++) {
                $seat_id = chr(65 + $i) . $j;
                $conn->query("INSERT INTO seats (coach_id, seat_id, seat_status) VALUES ('$coach_id', '$seat_id', 'available')");
            }
        }
        $message = "New bus added successfully!";
    } else {
        $message = "Error adding bus: " . $conn->error;
    }
}

// Fetch booking details
$bookings = $conn->query("SELECT b.booking_id, b.username, b.coach_id, r.leaving_from, r.destination, b.journey_date, r.fare, bs.departure_time 
                          FROM book b 
                          JOIN Bus bs ON b.coach_id = bs.coach_id 
                          JOIN routes r ON bs.route_id = r.route_id");

// Fetch routes
$routes = $conn->query("SELECT * FROM routes");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f8f9fa; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table th, table td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        table th { background-color: #007bff; color: white; }
        form { margin-bottom: 20px; }
        input, select, button { padding: 10px; margin: 10px 0; width: 100%; }
        button { background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #218838; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin Dashboard</h2>

        <!-- Booking Details -->
        <h3>Booking Details</h3>
        <table>
            <tr>
                <th>Booking ID</th>
                <th>Username</th>
                <th>Coach ID</th>
                <th>Departure Time</th>
                <th>Route</th>
                <th>Journey Date</th>
                <th>Fare</th>
            </tr>
            <?php while ($booking = $bookings->fetch_assoc()): ?>
            <tr>
                <td><?= $booking['booking_id']; ?></td>
                <td><?= $booking['username']; ?></td>
                <td><?= $booking['coach_id']; ?></td>
                <td><?= $booking['departure_time']; ?></td>
                <td><?= $booking['leaving_from'] . " → " . $booking['destination']; ?></td>
                <td><?= $booking['journey_date']; ?></td>
                <td><?= $booking['fare']; ?></td>
            </tr>
            <?php endwhile; ?>
        </table>

        <!-- Add New Bus -->
        <h3>Add New Bus</h3>
        <?php if (isset($message)): ?>
            <p style="color: green;"><?= $message; ?></p>
        <?php endif; ?>
        <form method="POST">
            <label for="route_id">Select Route</label>
            <select name="route_id" id="route_id" required>
                <?php while ($route = $routes->fetch_assoc()): ?>
                <option value="<?= $route['route_id']; ?>"><?= $route['leaving_from'] . " → " . $route['destination']; ?></option>
                <?php endwhile; ?>
            </select>
            <label for="coach_id">Coach ID</label>
            <input type="text" name="coach_id" id="coach_id" placeholder="Enter Coach ID" required>
            <label for="departure_time">Departure Time</label>
            <input type="text" name="departure_time" id="departure_time" placeholder="e.g., 08:00 AM" required>
            <button type="submit" name="add_bus">Add Bus</button>
        </form>
    </div>
</body>
</html>

<?php $conn->close(); ?>
