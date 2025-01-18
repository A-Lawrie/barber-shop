<?php
include "includes/connect.php";
include 'includes/session_meanings.php';

session_start();

if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

$UserID = $_SESSION['userid'];

$query = "SELECT * FROM users WHERE UserID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $UserID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "0 results";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.bundle.min.js">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="row py-5 px-4"> <div class="col-md-5 mx-auto"> <!-- Profile widget --> <div class="bg-white shadow rounded overflow-hidden"> <div class="px-4 pt-0 pb-4 cover"> <div class="media align-items-end profile-head"> <div class="profile mr-3"><img src="uploads/<?php echo htmlspecialchars($row['ProfilePicture']);?>" alt="..." width="130" class="rounded mb-2 img-thumbnail"><a href="#" class="btn btn-outline-dark btn-sm btn-block">Edit profile</a></div> <div class="media-body mb-5 text-white"> <h4 class="mt-0 mb-0"><?php echo htmlspecialchars($row['FName']. " " . $row['LName']);?></h4> <p class="small mb-4"> Last Login : <i class="fas fa-map-marker-alt mr-2"></i><?php echo date('d/m/Y', strtotime($row['LastLogin']));?></p> </div> </div> </div> <div class="bg-light p-4 d-flex justify-content-end text-center"></div> <div class="px-4 py-3"> <h5 class="mb-0">About</h5> <div class="p-4 rounded shadow-sm bg-light"> <p class="font-italic mb-0"><?php echo htmlspecialchars($row['PhoneNum']) ;?></p> <p class="font-italic mb-0"><?php echo htmlspecialchars($row['Email']) ;?></p></div> </div> <div class="py-4 px-4"></div> </div> </div> </div>
</div>


</body>
</html>