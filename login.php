<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include_once 'db_connection.php';
$pageTitle = 'Login';
include 'header.php';

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: index.php");
            exit();
        } else {
            $error_message = "Invalid password. Please try again.";
        }
    } else {
        $error_message = "User not found. Please register.";
    }

    $stmt->close();
}

$conn->close();
?>
<div class="container-sm mt-5">
    <?php if ($error_message): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="form-group mb-2">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group mb-2">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary mb-2">Login</button>
    </form>
    <p>Don't have an account? <a href="signup.php">Register here</a>.</p>
</div>
<?php include('footer.php'); ?>
