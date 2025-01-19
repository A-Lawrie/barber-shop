<?php
// Include your database connection
require_once 'includes/connect.php';

if (isset($_POST['BarberID'])) {
    $BarberID = $_POST['BarberID'];
    
    // Prepare the SQL query
    $stmt = $conn->prepare("SELECT CalendlyLink FROM barbers WHERE BarberID = ?");
    $stmt->bind_param("i", $BarberID);
    $stmt->execute();
    $stmt->bind_result($CalendlyLink);
    
    // Fetch the result
    if ($stmt->fetch()) {
        echo $CalendlyLink;
    } else {
        echo "Error: Barber not found";
    }

    $stmt->close();
    $conn->close();
}
?>