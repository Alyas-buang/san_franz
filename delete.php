<?php include 'db.php'; ?>

<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete related sales first
    $conn->query("DELETE FROM sales WHERE item_id=$id");

    // Now delete item
    $conn->query("DELETE FROM inventory WHERE id=$id");
}

header("Location: inventory.php");
exit();
?>
