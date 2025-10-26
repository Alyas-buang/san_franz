<?php include 'db.php'; ?>

<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM inventory WHERE id=$id");
    $item = $result->fetch_assoc();
}

if (isset($_POST['update'])) {
    $id   = $_POST['id'];
    $code = $_POST['item_code'];
    $name = $_POST['item_name'];
    $qty  = $_POST['quantity'];
    $uc   = $_POST['unit_cost'];
    $srp  = $_POST['srp'];

    $conn->query("UPDATE inventory 
                  SET item_code='$code', item_name='$name', quantity=$qty, unit_cost=$uc, srp=$srp 
                  WHERE id=$id");

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Item</title>
</head>
<body>
<h2>Update Item</h2>
<form method="POST" action="">
    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
    <input type="text" name="item_code" value="<?php echo $item['item_code']; ?>" required>
    <input type="text" name="item_name" value="<?php echo $item['item_name']; ?>" required>
    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" required>
    <input type="number" step="0.01" name="unit_cost" value="<?php echo $item['unit_cost']; ?>" required>
    <input type="number" step="0.01" name="srp" value="<?php echo $item['srp']; ?>" required>
    <button type="submit" name="update">Update</button>
</form>
<a href="index.php">⬅ Back to Inventory</a>
</body>
</html>
