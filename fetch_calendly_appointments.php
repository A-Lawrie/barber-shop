<?php
include 'includes/connect.php';
include 'apiKey.php';

// Calendly API details
$eventLink = 'https://api.calendly.com/event_types/2cad1858-1c25-4b52-a629-3a7968e986a0';


// Fetch scheduled events for the user
$user_uri = 'https://api.calendly.com/users/5af2685c-bb0c-4d4d-8d5b-abd05a3412a2'; // Replace with your user URI
$events_url = 'https://api.calendly.com/scheduled_events?user=' . urlencode($user_uri);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $events_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $api_key,
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
curl_close($ch);

if ($response === false) {
    die('Error fetching data from Calendly.');
}

$data = json_decode($response, true);



// Find the event matching the given event link
$eventDetails = null;
foreach ($data['collection'] as $event) {
    if ($event['event_type'] === $eventLink) {
        $eventDetails = $event;
        break;
    }
}

if (!$eventDetails) {
    die("Event not found.");
}

// Get invitee details for the specific event
$eventUri = $eventDetails['uri']; // Get the event URI
$invitees_url = $eventUri . '/invitees';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $invitees_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $api_key,
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
curl_close($ch);

if ($response === false) {
    die('Error fetching invitee details from Calendly.');
}

$inviteeData = json_decode($response, true);



if (empty($inviteeData['collection'])) {
    die("No invitees found for the event.");
}

// Process invitee details
foreach ($inviteeData['collection'] as $invitee) {
    $appointmentID = uniqid(); // Generate unique ID
    $customerName = $invitee['name'];
    $customerEmail = $invitee['email'];
    $customerPhone = null; // Initialize phone variable

    // Check for additional question responses
    foreach ($invitee['questions_and_answers'] as $qa) {
        if (strtolower($qa['question']) === 'phone number') {
            $customerPhone = $qa['answer'];
            break;
        }
    }

    // Convert start and end times to GMT+3
    $startTime = (new DateTime($eventDetails['start_time'], new DateTimeZone('UTC')))
    ->setTimezone(new DateTimeZone('Europe/Moscow')) // Adjust to your GMT+3 timezone
    ->format('Y-m-d H:i:s');

    $endTime = (new DateTime($eventDetails['end_time'], new DateTimeZone('UTC')))
    ->setTimezone(new DateTimeZone('Europe/Moscow')) // Adjust to your GMT+3 timezone
    ->format('Y-m-d H:i:s');

    $status = 'Pending'; // Default status

    // Insert into database
    $sql = "INSERT INTO appointments (AppointmentID, CustomerName, CustomerEmail, CustomerPhone, StartTime, EndTime, Status) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssssss', $appointmentID, $customerName, $customerEmail, $customerPhone, $startTime, $endTime, $status);

    if ($stmt->execute()) {
        echo "Appointment for $customerName added successfully!<br>";
    } else {
        echo "Error: " . $stmt->error . "<br>";
    }
}

// Close the database connection
$conn->close();
?>
