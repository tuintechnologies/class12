<?php
session_start();
include_once 'db_connection.php';
include('header.php');
$stmt = $conn->prepare("SELECT * FROM banks");
$stmt->execute();
$result = $stmt->get_result();
?>
<div class="container mt-2">
    <h2>All Items</h2>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                ?>
                <div class="col">
                    <div class="card h-100">
                        <img src="<?php echo $row['image']; ?>" class="card-img-top" alt="Image">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row['name']; ?></h5>
                            <p class="card-text">Established Date: <?php echo $row['established_date']; ?></p>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            ?>
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <p class="card-text">No items found.</p>
                    </div>
                </div>
            </div>
            <?php
        }
        $stmt->close();
        $conn->close();
        ?>
    </div>
</div>
<?php include('footer.php'); ?>
