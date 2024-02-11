<?php
session_start();

// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include_once 'db_connection.php';
$pageTitle = 'Edit Profile';
include 'header.php';

// Fetch user data from the database
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, email, mobile FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];

    // Check if the new email is already in use by other users
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    $email_check_result = $stmt->get_result();

    // Check if the new mobile number is already in use by other users
    $stmt = $conn->prepare("SELECT * FROM users WHERE mobile = ? AND id != ?");
    $stmt->bind_param("si", $mobile, $user_id);
    $stmt->execute();
    $mobile_check_result = $stmt->get_result();

    // Check if any records found for email and mobile
    if ($email_check_result->num_rows > 0) {
        $error_message = "Email already exists. Please choose a different email.";
    } elseif ($mobile_check_result->num_rows > 0) {
        $error_message = "Phone number already exists. Please choose a different phone number.";
    } else {
        // Update user information in the database
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, mobile = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $email, $mobile, $user_id);

        if ($stmt->execute()) {
            // Update successful, redirect to profile page
            header("Location: profile.php");
            exit();
        } else {
            // Error updating profile, set error message
            $error_message = "Failed to update profile. Please try again.";
        }
    }

    $stmt->close();
}
?>
<div class="container mt-5">
    <h2>Edit Profile</h2>
    <?php if (isset($error_message)): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group mb-2">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo $user['name']; ?>">
        </div>
        <div class="form-group mb-2">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>">
        </div>
        <div class="form-group mb-2">
            <label for="mobile">Mobile:</label>
            <input type="text" class="form-control" id="mobile" name="mobile" value="<?php echo $user['mobile']; ?>">
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Save Change</button>
    </form>
</div>
<?php include 'footer.php'; ?>
