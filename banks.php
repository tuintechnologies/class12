<?php
session_start();

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include_once "db_connection.php";
include 'header.php';
$pageTitle = 'Banks';
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM banks WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<div class="container">
    <h2>Bank Data List</h2>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Image</th>
                <th>Established Date</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
            </thead>
            <tbody>
            <?php
            // Check if there are rows returned
            if ($result->num_rows > 0) {
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><?php echo $row["id"]; ?></td>
                        <td><?php echo $row["name"]; ?></td>
                        <td><img src="<?php echo $row["image"]; ?>" alt="Image" style="max-width: 50px;"></td>
                        <td><?php echo $row["established_date"]; ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo $row["id"]; ?>" class="btn btn-primary btn-sm">Edit</a>
                        </td>
                        <td>
                            <form action="delete.php" method="post" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                <input type="hidden" name="id" value="<?php echo $row["id"]; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="6">No data found for this user.</td>
                </tr>
                <?php
            }

            // Close statement and database connection
            $stmt->close();
            $conn->close();
            ?>
            </tbody>
        </table>
    </div>
</div>
<?php
include 'footer.php';
?>
