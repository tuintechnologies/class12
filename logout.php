<?php
//if (isset($_POST['logout'])) {
//    session_unset();
//    session_destroy();
//    header("Location: login.php");
//    exit();
//}
//?>

<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Perform any necessary cleanup or logging out actions

    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to the login page or home page
    header("Location: login.php"); // You can change this to your preferred redirect page
    exit();
} else {
    header("Location: login.php");
    exit();
}
?>
