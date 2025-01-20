<?php
include 'includes/connect.php';

// Get the raw POST data from Calendly
$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

// Verify the webhook is for the correct event type
$eventType = 'https://api.calendly.com/event_types/2cad1858-1c25-4b52-a629-3a7968e986a0';
if ($data['event'] !== 'invitee.created' || $data['payload']['event_type'] !== $eventType) {
    http_response_code(400); // Bad Request
    exit('Invalid event type');
}

// Extract invitee details
$invitee = $data['payload']['invitee'];
$startTime = (new DateTime($data['payload']['start_time'], new DateTimeZone('UTC')))
    ->setTimezone(new DateTimeZone('Europe/Moscow'))
    ->format('Y-m-d H:i:s');
$endTime = (new DateTime($data['payload']['end_time'], new DateTimeZone('UTC')))
    ->setTimezone(new DateTimeZone('Europe/Moscow'))
    ->format('Y-m-d H:i:s');

$appointmentID = uniqid();
$customerName = $invitee['name'];
$customerEmail = $invitee['email'];
$customerPhone = null;

// Extract custom questions (if any)
foreach ($data['payload']['questions_and_answers'] as $qa) {
    if (strtolower($qa['question']) === 'phone number') {
        $customerPhone = $qa['answer'];
        break;
    }
}

$status = 'Pending';

// Insert the appointment into the database
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
