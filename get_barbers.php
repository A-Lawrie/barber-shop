<?php
// Include your database connection
require_once 'includes/connect.php';

// Query to get all departments
$query = "SELECT BarberID, CONCAT(FName, ' ', LName) AS name FROM barbers";
$result = $conn->query($query);


?>
    <h4 style="font-weight: bold; color: white; font-family: sans-serif;">Choose a Barber</h4>
<?php

if ($result->num_rows > 0) {
    echo '<select id="barberSelect" name="barber">';
    echo '<option value="">Select a Barber</option>'; 

    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . $row['BarberID'] . '">' . $row['name'] . '</option>';
    }

    echo '</select>';
} else {
    echo 'No barbers found.';
}

$conn->close();
?>
