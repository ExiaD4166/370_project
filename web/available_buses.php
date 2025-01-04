<?php
session_start();  // Start the session

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    // Redirect to login if not logged in
    header("Location: signup.html?message=Please login first.");
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

if (isset($_GET['starting'], $_GET['destination'], $_GET['date'])) {
    $starting = strtolower(trim($_GET['starting']));
    $destination = strtolower(trim($_GET['destination']));
    $date = $_GET['date'];

    // Route Query
    $route_sql = "SELECT route_id, fare FROM routes 
                  WHERE LOWER(TRIM(leaving_from)) = '$starting' 
                  AND LOWER(TRIM(destination)) = '$destination'";
    $route_result = $conn->query($route_sql);

    if ($route_result && $route_result->num_rows > 0) {
        $route = $route_result->fetch_assoc();
        $route_id = $route['route_id'];
        $fare = $route['fare'];

        // Bus Query
        $bus_sql = "SELECT * FROM bus WHERE route_id = $route_id ORDER BY coach_id ASC";
        $bus_result = $conn->query($bus_sql);

        if ($bus_result && $bus_result->num_rows > 0) {
            echo "<h2>Available Buses</h2>";
            echo "<table border='1' style='width: 100%; border-collapse: collapse;'>
                    <tr>
                        <th>Coach ID</th>
                        <th>Bus Type</th>
                        <th>Departure Time</th>
                        <th>Total Seats</th>
                        <th>Fare</th>
                        <th>Action</th>
                    </tr>";

            while ($bus = $bus_result->fetch_assoc()) {
                $coach_id = $bus['coach_id'];
                $bus_type = htmlspecialchars($bus['bus_type']);
                $departure_time = htmlspecialchars($bus['departure_time']);
                $total_seats = $bus['total_seats'];

                echo "<tr>
                        <td>{$coach_id}</td>
                        <td>{$bus_type}</td>
                        <td>{$departure_time}</td>
                        <td>{$total_seats}</td>
                        <td>{$fare}</td>
                        <td><a href=\"select_seat.php?coach_id={$coach_id}&route_id={$route_id}&fare={$fare}&date={$date}\">Select Seat</a></td>
                      </tr>";
            }

            echo "</table>";
        } else {
            echo "No buses available for the selected route.";
        }
    } else {
        echo "No routes found for the selected locations.";
    }
} else {
    echo "Invalid request. Please select a starting location, destination, and date.";
}

$conn->close();
?>
