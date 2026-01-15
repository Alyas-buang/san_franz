<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<?php
include 'db.php';

/* -------------------------------
   ADD / UPDATE ITEM (SAFE)
-------------------------------- */
if (isset($_POST['add'])) {
    $code = trim($_POST['item_code']);
    $name = trim($_POST['item_name']);
    $qty  = (int)$_POST['quantity'];
    $uc   = (float)$_POST['unit_cost'];
    $srp  = (float)$_POST['srp'];

    // Check if exists
    $stmt = $conn->prepare("SELECT id FROM inventory WHERE item_code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update
        $update = $conn->prepare("
            UPDATE inventory 
            SET quantity = quantity + ?, item_name = ?, unit_cost = ?, srp = ?
            WHERE item_code = ?
        ");
        $update->bind_param("isdss", $qty, $name, $uc, $srp, $code);
        $update->execute();
    } else {
        // Insert
        $insert = $conn->prepare("
            INSERT INTO inventory (item_code, item_name, quantity, unit_cost, srp)
            VALUES (?, ?, ?, ?, ?)
        ");
        $insert->bind_param("ssidd", $code, $name, $qty, $uc, $srp);
        $insert->execute();
    }

    // Prevent resubmission
    header("Location: inventory.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>JB Builders Inventory</title>
<style>
body { font-family: Arial; background:#f4f6f8; }
.container {
  width: 90%; max-width: 1100px; margin: 30px auto;
  background: #fff; padding: 25px;
  border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,.1);
}
h2 { text-align:center; color:#007bff; }
button {
  background:#007bff; color:#fff;
  border:none; padding:7px 12px;
  border-radius:6px; cursor:pointer;
}
button:hover { background:#0056b3; }
input { padding:7px; border-radius:6px; border:1px solid #ccc; }
table { width:100%; border-collapse:collapse; margin-top:15px; }
th,td { border:1px solid #ddd; padding:10px; text-align:center; }
th { background:#007bff; color:#fff; }
.low-stock { background:#ffe5e5 !important; color:#b00000; font-weight:bold; }
</style>
</head>

<body>
<div class="container">

<h2>San Francisco Inventory System</h2>

<div style="margin-bottom:15px;">
  <a href="sales_report.php"><button>Sales Report</button></a>
  <a href="cashier.php"><button>Cashier</button></a>
</div>

<form method="POST">
  <input name="item_code" placeholder="Code" required>
  <input name="item_name" placeholder="Name" required>
  <input type="number" name="quantity" placeholder="Qty" required>
  <input type="number" step="0.01" name="unit_cost" placeholder="Unit Cost" required>
  <input type="number" step="0.01" name="srp" placeholder="SRP" required>
  <button name="add">Add / Update</button>
</form>

<h3>Inventory List</h3>
<table>
<tr>
  <th>Code</th><th>Name</th><th>Qty</th><th>Cost</th><th>SRP</th><th>Action</th>
</tr>

<?php
$res = $conn->query("SELECT * FROM inventory ORDER BY item_name ASC");
while ($row = $res->fetch_assoc()):
$low = $row['quantity'] <= 5 ? 'low-stock' : '';
?>
<tr class="<?= $low ?>">
  <td><?= htmlspecialchars($row['item_code']) ?></td>
  <td><?= htmlspecialchars($row['item_name']) ?></td>
  <td><?= $row['quantity'] ?></td>
  <td><?= number_format($row['unit_cost'],2) ?></td>
  <td><?= number_format($row['srp'],2) ?></td>
  <td>
    <form action="sale.php" method="POST" style="display:inline">
      <input type="hidden" name="item_id" value="<?= $row['id'] ?>">
      <input type="number" name="qty_sold" required style="width:60px">
      <button name="sell">Sell</button>
    </form>
    <a href="update.php?id=<?= $row['id'] ?>"><button>Update</button></a>
    <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete item?')">
      <button>Delete</button>
    </a>
  </td>
</tr>
<?php endwhile; ?>

</table>
</div>
</body>
</html>
