<?php
session_start();
include "includes/connect.php";

date_default_timezone_set("Africa/Nairobi");
$date = date("Y-m-d H:i:s");
$filedate = date("Y_m_d_H_i_s");

if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/';
    $fileName = basename($_FILES['profile_photo']['name']);
    $targetFile = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $targetFile)) {
        // Update the database with the new file name
        $UserID = $_SESSION['userid'];
        $query = "UPDATE users SET ProfilePicture = ? WHERE UserID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $fileName, $UserID);
        $stmt->execute();

        echo json_encode(['success' => true, 'filename' => $fileName]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to upload file.']);
    }
} else {
    // No new file uploaded, retain the current profile picture
    echo json_encode(['success' => true, 'message' => 'No new profile photo uploaded. Current photo retained.']);
}


if(isset($_POST['login'])) {
    // Get user input
    $email = $_POST['email'];
    $password = $_POST['password'];

    $qry = $conn->prepare("SELECT * FROM users WHERE Email=?");
    $qry->bind_param("s", $email);
    if (!$qry->execute()) {
        $_SESSION['error'] = "Error retrieving user's account";
        session_write_close();
        header('location: login.php');
        exit();
    }
    $userres = $qry->get_result();

    if ($userres->num_rows > 0) {
        $user = $userres->fetch_assoc();
        if (password_verify($password, $user['Password'])) {
                $_SESSION['userid'] = $user['UserID'];
                $_SESSION['fname'] = $user['FName'];
                $_SESSION['lname'] = $user['LName'];
                $_SESSION['success'] = "Welcome, {$user['FName']}!";

                $sql = "UPDATE users SET LastLogin=? WHERE UserID=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $date, $user['UserID']);
                $stmt->execute();

                session_write_close();
                header('location: index.php');
        } else {
            $_SESSION['error'] = "Invalid Credentials";
            session_write_close();
            header('location: login.php');
            exit();
        }
    } else {
        $_SESSION['error'] = "Account Does Not Exist";
        session_write_close();
        header('location: login.php');
        exit();
    }
}elseif (isset($_POST['edit-user'])) {
    $UserId = $_SESSION['userid'];

    $email = $_POST['email'];
    $phone = $_POST['phone'];
    
    // Retrieve the submitted full name
    $fullName = trim($_POST['name']); // Trim to remove any leading/trailing spaces

    // Split the full name into first name and last name
    $nameParts = explode(' ', $fullName, 2); // Limit to 2 parts
    $FName = isset($nameParts[0]) ? $nameParts[0] : '';
    $LName = isset($nameParts[1]) ? $nameParts[1] : ''; 

    $query = "UPDATE users set FName = ?, LName = ?, Email = ?, PhoneNum = ? WHERE UserID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $FName, $LName, $email, $phone, $UserId);

    if ($stmt->execute()) {
        // Redirect or show a success message
        $_SESSION['success'] = "Profile updated successfully";
        header("Location: user-profile.php");
    } else {
        // Redirect or show an error message
        $_SESSION['error'] = "Failed to update profile";
        header("Location: edit-user.php");
    }
}

header('Content-Type: application/json');
?>

