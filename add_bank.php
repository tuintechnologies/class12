<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include_once 'db_connection.php';
include 'header.php';
$pageTitle = 'Add Bank';
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $user_id = $_SESSION['user_id'];

    $name = $_POST['name'];

    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check !== false) {
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            $error_message = "Sorry, only JPG, JPEG & PNG files are allowed.";
        } else {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_path = $target_file;
                $established_date = $_POST['established_date'];

                $stmt = $conn->prepare("INSERT INTO banks (name, image, established_date, user_id) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("sssi", $name, $image_path, $established_date, $user_id);
                $stmt->execute();

                header("Location: banks.php");
                exit();
            } else {
                $error_message = "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        echo "File is not an image.";
    }
}
$conn->close();
?>
<div class="container mt-2">
    <?php if ($error_message): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <h2>Add Item</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <div class="form-group mb-2">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group mb-2">
            <label for="image">Image:</label>
            <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
        </div>
        <div class="form-group mb-2">
            <label for="established_date">Established Date:</label>
            <input type="date" class="form-control" id="established_date" name="established_date" required>
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Add Item</button>
    </form>
</div>
<?php
include 'footer.php';
?>
