<?php
session_start();
include "includes/connect.php";

date_default_timezone_set("Africa/Nairobi");
$date = date("Y-m-d H:i:s");
$filedate = date("Y_m_d_H_i_s");

if (isset($_FILES['profile_photo'])) {
    $uploadDir = 'uploads/';
    $fileName = basename($_FILES['profile_photo']['name']);
    $targetFile = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $targetFile)) {
        // Update the database with the new file name
        // Assume $conn is your database connection
        $UserID = $_SESSION['userid'];
        $query = "UPDATE users SET ProfilePicture = ? WHERE UserID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $fileName, $UserID);
        $stmt->execute();

        echo json_encode(['success' => true, 'filename' => $fileName]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to upload file.']);
    }
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
}elseif (isset($_POST['edit-user']) && isset($_FILES['profile_photo'])) {
    $UserId = $_SESSION['userid'];
    $file = $_FILES['profile_photo'];
    $uploadDir = 'uploads/';
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];

    if (in_array($file['type'], $allowedTypes)) {
        $filename = uniqid() . '-' . basename($file['name']);
        $targetFile = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            // Update the database
            $sql = "UPDATE users SET ProfilePicture = ? WHERE UserID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('si', $filename, $userId);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['filename'] = $filename;
            } else {
                $response['error'] = 'Failed to update database.';
            }
        } else {
            $response['error'] = 'Failed to upload file.';
        }
    } else {
        $response['error'] = 'Invalid file type. Only JPEG, PNG, and GIF are allowed.';
    }
} else {
    $response['error'] = 'No file uploaded.';
}

header('Content-Type: application/json');
echo json_encode($response);
?>

