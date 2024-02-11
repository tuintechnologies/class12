<?php
session_start();

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include_once 'db_connection.php';
$pageTitle = 'Profile';
include 'header.php';

// Fetch user information from the database
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id, name, email, mobile, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$conn->close();
?>

<div class="container">
    <h2>Welcome, <?php echo $user['name']; ?>!</h2>
    <p>Email: <?php echo $user['email']; ?></p>
    <p>Mobile: <?php echo $user['mobile']; ?></p>
    <p>Register At: <?php echo $user['created_at']; ?></p>
    <p><a href="logout.php">Logout</a></p>
    <p><a href="profile_edit.php">Edit Profile</a></p>
    <p><a href="change_password.php">Change Password</a></p>
</div>

<?php include 'footer.php'; ?>
