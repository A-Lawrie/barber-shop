<?php
session_start();
include "includes/connect.php";

date_default_timezone_set("Africa/Nairobi");
$date = date("Y-m-d H:i:s");
$filedate = date("Y_m_d_H_i_s");

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
}
