<?php

session_start();

include('config.php');

// Create connection
$DBConnect = @new mysqli("localhost", "root", "", "database");

// Check connection
if ($DBConnect->connect_error) {
    die("Connection failed: " . $DBConnect->connect_error);
}

if (isset($_POST['button'])) {
    $fisrt_parm = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user details from the database
    $sql_query = "SELECT User_ID, FULL_NAME, USER_NAME, USER_TYPE, EMAIL, IMAGE, FACEBOOK, WHATSAPP, BIO, FALLOWERS, FALLOWING, POSTS, PASSWORD_S FROM USERS WHERE (USER_NAME = ? OR EMAIL = ?)";
    $stmt = $DBConnect->prepare($sql_query);
    if ($stmt === false) {
        die("Prepare failed: " . $DBConnect->error);
    }
    $stmt->bind_param('ss', $fisrt_parm, $fisrt_parm);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows() > 0) {
        $stmt->bind_result($user_id, $full_name, $user_name, $user_type, $email_address, $image, $facebook, $whatsapp, $bio, $fallowers, $fallowing, $post_count, $stored_password);
        $stmt->fetch();

        // Check if the entered password matches the stored password
        if ($password === $stored_password) {
            // Password is correct, store user info in session
            $_SESSION['id'] = $user_id;
            $_SESSION['username'] = $user_name;
            $_SESSION['fullname'] = $full_name;
            $_SESSION['email'] = $email_address;
            $_SESSION['usertype'] = $user_type;
            $_SESSION['facebook'] = $facebook;
            $_SESSION['whatsapp'] = $whatsapp;
            $_SESSION['bio'] = $bio;
            $_SESSION['fallowers'] = $fallowers;
            $_SESSION['fallowing'] = $fallowing;
            $_SESSION['postcount'] = $post_count;
            $_SESSION['img_path'] = $image;

            header("location: home.php");
        } else {
            header('location: login.php?error_message=Email/Password Incorrect');
        }
    } else {
        header('location: login.php?error_message=Email/Password Incorrect');
    }

    $stmt->close(); // Close statement
    $DBConnect->close(); // Close connection

    exit;
} else {
    header('location: login.php?error_message=Some Error Occurred');
    exit;
}
