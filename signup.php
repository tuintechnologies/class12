<?php
session_start();
include_once("db_connection.php");

$error_message = "";

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $mobile = $_POST['mobile'];

    $email_check_sql = "SELECT * FROM users WHERE email='$email'";
    $mobile_check_sql = "SELECT * FROM users WHERE mobile='$mobile'";

    $email_check_result = $conn->query($email_check_sql);
    $mobile_check_result = $conn->query($mobile_check_sql);

    if ($email_check_result->num_rows > 0) {
        $error_message = "Email already exists. Please choose a different email.";
    } elseif ($mobile_check_result->num_rows > 0) {
        $error_message = "Phone number already exists. Please choose a different phone number.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $register_sql = "INSERT INTO users (name, email, password, mobile) VALUES ('$name', '$email', '$hashed_password', '$mobile')";

        if ($conn->query($register_sql) === TRUE) {
            $_SESSION['user_id'] = $conn->insert_id;
            header("Location: index.php");
            exit();
        } else {
            $error_message = "Error registering user. Please try again.";
        }
    }
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
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="mobile">Mobile:</label>
            <input type="text" class="form-control" id="mobile" name="mobile" required>
        </div>
        <button type="submit" name="register" class="btn btn-primary">Register</button>
    </form>
</div>
<?php include('footer.php'); ?>

