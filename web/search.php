<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    // Redirect to login page if not logged in
    header("Location: signup.html?message=Please login first.");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Buses</title>
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
        select, input, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .my-bookings {
            background-color: #28a745;
            color: white;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Search for Buses</h2>
        <form action="available_buses.php" method="GET">
    <label for="starting">Starting Location</label>
    <select name="starting" id="starting">
        <option value="dhaka">Dhaka</option>
        <option value="sylhet">Sylhet</option>
        <option value="chittagong">Chittagong</option>
        <option value="rajshahi">Rajshahi</option>
        <option value="khulna">Khulna</option>
    </select>
    <label for="destination">Destination</label>
    <select name="destination" id="destination">
        <option value="dhaka">Dhaka</option>
        <option value="sylhet">Sylhet</option>
        <option value="chittagong">Chittagong</option>
        <option value="rajshahi">Rajshahi</option>
        <option value="khulna">Khulna</option>
    </select>
    <label for="date">Date</label>
    <input type="date" name="date" id="date" required>
    <button type="submit">Search</button>
</form>
        <button class="my-bookings" onclick="window.location.href='my_bookings.php';">My Bookings</button>
    </div>
</body>
</html>
