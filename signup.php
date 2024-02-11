<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
include_once "db_connection.php";

$error_message = "";

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $mobile = $_POST['mobile'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $email_check_result = $stmt->get_result();

    $stmt = $conn->prepare("SELECT * FROM users WHERE mobile = ?");
    $stmt->bind_param("s", $mobile);
    $stmt->execute();
    $mobile_check_result = $stmt->get_result();

    if ($email_check_result->num_rows > 0) {
        $error_message = "Email already exists. Please choose a different email.";
    } elseif ($mobile_check_result->num_rows > 0) {
        $error_message = "Phone number already exists. Please choose a different phone number.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        // Insert user using prepared statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, mobile) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $hashed_password, $mobile);

        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            header("Location: index.php");
            exit();
        } else {
            $error_message = "Error registering user. Please try again.";
        }
    }

    $stmt->close();
}

$conn->close();
$pageTitle = 'Signup';
include('header.php');
?>

<div class="container mt-5">
    <?php if ($error_message): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="form-group mb-2">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group mb-2">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group mb-2">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group mb-2">
            <label for="mobile">Mobile:</label>
            <input type="text" class="form-control" id="mobile" name="mobile" required>
        </div>
        <button type="submit" name="register" class="btn btn-primary">Register</button>
    </form>
    <p>Already have a account? <a href="login.php">Login here</a>.</p>
</div>
<?php include('footer.php'); ?>

