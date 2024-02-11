<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include_once 'db_connection.php';
$pageTitle = 'Change Password';
include 'header.php';

$error_message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $user_id = $_SESSION['user_id'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!password_verify($old_password, $user['password'])) {
        $error_message = "Incorrect old password. Please try again.";
    } elseif ($new_password !== $confirm_password) {
        $error_message = "New password and confirm password do not match.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);

        if ($stmt->execute()) {
            session_unset();
            session_destroy();
            header("Location: login.php");
            exit();
        } else {
            $error_message = "Failed to change password. Please try again.";
        }
    }
    $stmt->close();
}

$conn->close();
?>
<div class="container mt-5">
    <h2>Change Password</h2>
    <?php if ($error_message): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group mb-2">
            <label for="old_password">Old Password:</label>
            <input type="password" class="form-control" id="old_password" name="old_password" required>
        </div>
        <div class="form-group mb-2">
            <label for="new_password">New Password:</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required>
        </div>
        <div class="form-group mb-2">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-primary mb-2" name="submit">Change Password</button>
    </form>
</div>
<?php include('footer.php'); ?>
