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

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Check if required GET parameters are provided
if (!isset($_GET['coach_id'], $_GET['route_id'], $_GET['fare'])) {
    die("Missing parameters: coach_id, route_id, or fare.");
}

$coach_id = intval($_GET['coach_id']);
$route_id = intval($_GET['route_id']);
$fare = intval($_GET['fare']);

// Fetch seat information
$sql = "SELECT seat_id, seat_status FROM seats WHERE coach_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $coach_id);

if (!$stmt->execute()) {
    die("Error executing query: " . $stmt->error);
}

$result = $stmt->get_result();

// Check if the result has any rows
if ($result->num_rows == 0) {
    die("No seats found for coach_id = " . $coach_id . ". Please check if the coach exists in the database.");
}

$seats = [];
while ($row = $result->fetch_assoc()) {
    $seats[] = $row;
}

$stmt->close();
$conn->close();

// Generate a unique booking ID
$booking_id = uniqid();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Seat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .seat {
            display: inline-block;
            width: 40px;
            height: 40px;
            margin: 5px;
            text-align: center;
            line-height: 40px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .available {
            background-color: #28a745;
            color: white;
        }
        .booked {
            background-color: #dc3545;
            color: white;
            cursor: not-allowed;
        }
        .row {
            display: flex;
            justify-content: center;
        }
        .legend {
            margin: 20px 0;
        }
        .legend div {
            display: inline-block;
            margin-right: 10px;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .legend .available {
            background-color: #28a745;
        }
        .legend .booked {
            background-color: #dc3545;
        }
        .book-button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .book-button:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Select Seat for Coach ID: <?= $coach_id ?></h2>
        <p>Fare: <?= $fare ?> per seat</p>

        <div class="legend">
            <div class="available">Available</div>
            <div class="booked">Booked</div>
        </div>

        <form id="seatForm" action="booking_summary.php" method="POST">
            <input type="hidden" name="coach_id" value="<?= $coach_id ?>">
            <input type="hidden" name="route_id" value="<?= $route_id ?>">
            <input type="hidden" name="fare" value="<?= $fare ?>">
            <input type="hidden" name="booking_id" value="<?= $booking_id ?>">

            <?php
            $rows = 10; // Maximum number of rows
            $cols = 4; // Number of columns in each row
            $seatMap = []; // Map seat ID to seat status

            // Populate the seat map from the fetched seats
            foreach ($seats as $seat) {
                $seatMap[$seat['seat_id']] = $seat['seat_status'];
            }

            for ($i = 1; $i <= $rows; $i++) {
                $rowLabel = chr(64 + $i); // Generate row labels (A, B, C, ...)

                echo '<div class="row">';
                for ($j = 1; $j <= $cols; $j++) {
                    $seatLabel = $rowLabel . $j; // Generate seat label (A1, A2, ...)
                    if (isset($seatMap[$seatLabel])) {
                        $seatStatus = $seatMap[$seatLabel];
                        $class = ($seatStatus === 'booked') ? 'booked' : 'available';
                        $disabled = ($seatStatus === 'booked') ? 'disabled' : '';
                        echo "<label class='seat $class'>
                                <input type='checkbox' name='selected_seats[]' value='$seatLabel' $disabled style='display:none;'>$seatLabel
                              </label>";
                    }
                }
                echo '</div>';
            }
            ?>

            <button type="submit" class="book-button" id="bookButton" disabled>Book Selected Seats</button>
        </form>
    </div>

    <script>
        const form = document.getElementById('seatForm');
        const bookButton = document.getElementById('bookButton');

        form.addEventListener('change', () => {
            const selectedSeats = form.querySelectorAll('input[name="selected_seats[]"]:checked');
            bookButton.disabled = selectedSeats.length === 0;
        });
    </script>
</body>
</html>