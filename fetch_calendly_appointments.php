<?php
include 'includes/connect.php';

$eventLink = 'https://api.calendly.com/event_types/2cad1858-1c25-4b52-a629-3a7968e986a0';

$api_key = 'eyJraWQiOiIxY2UxZTEzNjE3ZGNmNzY2YjNjZWJjY2Y4ZGM1YmFmYThhNjVlNjg0MDIzZjdjMzJiZTgzNDliMjM4MDEzNWI0IiwidHlwIjoiUEFUIiwiYWxnIjoiRVMyNTYifQ.eyJpc3MiOiJodHRwczovL2F1dGguY2FsZW5kbHkuY29tIiwiaWF0IjoxNzM3MzE3NTEyLCJqdGkiOiJmMGVlZjNmYi1iM2U0LTRmNTAtODFhMy02MjRkZDVjYjMyNzAiLCJ1c2VyX3V1aWQiOiI1YWYyNjg1Yy1iYjBjLTRkNGQtOGQ1Yi1hYmQwNWEzNDEyYTIifQ.V3fgP7coaZUKxhOToB0NHiSiCVnnmhE2dIlhBEFYblqb6PJmGN0J6R3gD4Ro7heMWl9Wh6VtODav5sdq3X9Nag';  
$user_uri = 'https://api.calendly.com/users/5af2685c-bb0c-4d4d-8d5b-abd05a3412a2'; // Replace with your user URI
$url = 'https://api.calendly.com/scheduled_events?user=' . urlencode($user_uri);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $api_key,
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
curl_close($ch);

if ($response === false) {
    die('Error fetching data from Calendly: ' . curl_error($ch));
}

$data = json_decode($response, true);

echo '<pre>';
print_r($data);
echo '</pre>';
exit;


// Filter for the specific event
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

// Extract data for the database
$appointmentID = uniqid(); // Generate unique ID
$customerName = $eventDetails['invitee']['name'];
$customerEmail = $eventDetails['invitee']['email'];
$customerPhone = null; // Initialize phone variable

// Check for additional question responses
foreach ($eventDetails['questions_and_answers'] as $qa) {
    if (strtolower($qa['question']) === 'phone number') {
        $customerPhone = $qa['answer'];
        break;
    }
}

$startTime = $eventDetails['start_time'];
$endTime = $eventDetails['end_time'];
$status = 'Pending'; // Default status

// Insert into database
$sql = "INSERT INTO appointments (AppointmentID, CustomerName, CustomerEmail, CustomerPhone, StartTime, EndTime, Status) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param('sssssss', $appointmentID, $customerName, $customerEmail, $customerPhone, $startTime, $endTime, $status);

if ($stmt->execute()) {
    echo "Appointment added successfully!";
} else {
    echo "Error: " . $stmt->error;
}

// Close the database connection
$conn->close();
?>
