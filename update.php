<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<?php
include 'db.php';

/* ---------------------------
   FETCH ITEM (SAFE)
---------------------------- */
if (!isset($_GET['id'])) {
    header("Location: inventory.php");
    exit;
}

$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM inventory WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Item not found.");
}

$item = $result->fetch_assoc();

/* ---------------------------
   UPDATE ITEM (SAFE)
---------------------------- */
if (isset($_POST['update'])) {
    $code = trim($_POST['item_code']);
    $name = trim($_POST['item_name']);
    $qty  = (int)$_POST['quantity'];
    $uc   = (float)$_POST['unit_cost'];
    $srp  = (float)$_POST['srp'];

    $update = $conn->prepare("
        UPDATE inventory 
        SET item_code = ?, item_name = ?, quantity = ?, unit_cost = ?, srp = ?
        WHERE id = ?
    ");
    $update->bind_param("ssiddi", $code, $name, $qty, $uc, $srp, $id);
    $update->execute();

    header("Location: inventory.php?updated=1");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Update Item | JB Builders</title>
<link rel="stylesheet" href="style.css">
</head>

<body>
<div class="container">
<h2>Update Item</h2>

<form method="POST">
  <input type="text" name="item_code"
         value="<?= htmlspecialchars($item['item_code']) ?>" required>

  <input type="text" name="item_name"
         value="<?= htmlspecialchars($item['item_name']) ?>" required>

  <input type="number" name="quantity"
         value="<?= $item['quantity'] ?>" required>

  <input type="number" step="0.01" name="unit_cost"
         value="<?= $item['unit_cost'] ?>" required>

  <input type="number" step="0.01" name="srp"
         value="<?= $item['srp'] ?>" required>

  <button name="update">Update Item</button>
  <a href="inventory.php"><button type="button" class="btn-danger">Cancel</button></a>
</form>
</div>
</body>
</html>
