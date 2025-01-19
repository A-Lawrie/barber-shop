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

    <style>
        .rounded-circle {
            border-radius: 50% !important;
            cursor: pointer;
        }
        .profile img {
            border: 2px solid #ccc;
            width: 130px;
            height: 130px;
            object-fit: cover;
        }
        #photo-input {
            display: none;
        }
    </style>
</head>
<body>
    <div class="row py-5 px-4">
         <div class="col-md-5 mx-auto">
            <form action="processes.php" method="POST" enctype="multipart/form-data">
             <!-- Profile widget -->
               <div class="bg-white shadow rounded overflow-hidden">
                 <div class="px-4 pt-0 pb-4 cover">
                     <div class="media align-items-end profile-head"> 
                        <div class="profile mr-3">
                        <img id="profile-picture" 
                                 src="uploads/<?php echo htmlspecialchars($row['ProfilePicture']); ?>" 
                                 alt="Profile Picture for <?php echo htmlspecialchars($row['FName']. " " . $row['LName']);?>" width="130" 
                                 class="rounded mb-2 img-thumbnail">
                            <input type="file" id="photo-input" name="profile_photo" accept="image/*">
                        </div> 
                        <div class="media-body mb-5 text-white"> 
                            <label for="name">Edit name</label>
                             <input type="text" name="name" value="<?php echo htmlspecialchars($row['FName']. " " . $row['LName']);?>"> 
                             <br><br>
                        </div> 
                    </div> 
                </div> 
                <div class="bg-light p-4 d-flex justify-content-end text-center"></div> 
                <div class="px-4 py-3"> 
                    <h5 class="mb-0">About</h5>
                     <div class="p-4 rounded shadow-sm bg-light"> 
                        <label for="phone">Edit Phone Number</label>
                        <input class="font-italic mb-0" type="text" value="<?php echo htmlspecialchars($row['PhoneNum']) ;?>" name="phone">

                        <label for="email">Edit Email</label>
                        <input class="font-italic mb-0" type="text" value="<?php echo htmlspecialchars($row['Email']) ;?>" name="email">
                    </div> 
                </div> 
                
                <div class="py-4 px-4">
                <input style="background-color: peachpuff; font-weight:bold;" type="submit" name="update" value="Update Profile">
                </div>
               
             </div> 
            </form>
            </div> 
        </div>
</div>

<script>
        // Handle clicking on the profile picture to open the file input
        document.getElementById('profile-picture').addEventListener('click', function () {
            document.getElementById('photo-input').click();
        });

        // Automatically upload the selected file
        document.getElementById('photo-input').addEventListener('change', function () {
            const formData = new FormData();
            const fileInput = this.files[0];

            if (fileInput) {
                formData.append('profile_photo', fileInput);

                fetch('processes.php', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update the displayed profile picture
                        document.getElementById('profile-picture').src = `uploads/${data.filename}`;
                        alert('Profile picture updated successfully!');
                    } else {
                        alert('Error updating profile picture: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });
    </script>
</body>
</html>