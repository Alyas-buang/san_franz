<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<?php
include 'db.php';

/* -----------------------------
   DATE FILTER (optional)
------------------------------ */
$from = $_GET['from'] ?? '';
$to   = $_GET['to'] ?? '';

$sql = "
SELECT i.item_code, i.item_name, s.quantity_sold,
       i.unit_cost, i.srp, s.date_of_sale
FROM sales s
JOIN inventory i ON s.item_id = i.id
WHERE 1
";

$params = [];
$types  = "";

if ($from && $to) {
    $sql .= " AND s.date_of_sale BETWEEN ? AND ?";
    $types = "ss";
    $params = [$from, $to];
}

$sql .= " ORDER BY s.date_of_sale DESC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$grand_total = 0;
$profit_total = 0;
?>
<!DOCTYPE html>
<html>
<head>
<title>Sales Report | JB Builders</title>
<style>
body { font-family: Arial; background:#f4f6f8; }
.container {
  width:90%; max-width:1100px; margin:30px auto;
  background:#fff; padding:25px;
  border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,.1);
}
h2 { text-align:center; color:#007bff; }
button {
  background:#007bff; color:#fff;
  border:none; padding:7px 14px;
  border-radius:6px; cursor:pointer;
}
.back { background:#6c757d; }
table { width:100%; border-collapse:collapse; margin-top:15px; }
th,td { border:1px solid #ddd; padding:10px; text-align:center; }
th { background:#007bff; color:#fff; }
.total { font-weight:bold; }
.filter { margin:15px 0; text-align:right; }
@media print {
  button, .filter { display:none; }
  body { background:#fff; }
}
</style>
</head>

<body>
<div class="container">

<h2>Sales Report</h2>

<a href="inventory.php"><button class="back">⬅ Back</button></a>
<button onclick="window.print()">🖨 Print</button>

<form class="filter" method="GET">
  <input type="date" name="from" value="<?= htmlspecialchars($from) ?>">
  <input type="date" name="to" value="<?= htmlspecialchars($to) ?>">
  <button>Filter</button>
</form>

<table>
<tr>
  <th>Code</th>
  <th>Name</th>
  <th>Qty</th>
  <th>Unit Cost</th>
  <th>SRP</th>
  <th>Total Sales</th>
  <th>Profit</th>
  <th>Date</th>
</tr>

<?php while ($row = $result->fetch_assoc()):
  $total = $row['srp'] * $row['quantity_sold'];
  $profit = ($row['srp'] - $row['unit_cost']) * $row['quantity_sold'];
  $grand_total += $total;
  $profit_total += $profit;
?>
<tr>
  <td><?= htmlspecialchars($row['item_code']) ?></td>
  <td><?= htmlspecialchars($row['item_name']) ?></td>
  <td><?= $row['quantity_sold'] ?></td>
  <td>₱<?= number_format($row['unit_cost'],2) ?></td>
  <td>₱<?= number_format($row['srp'],2) ?></td>
  <td class="total">₱<?= number_format($total,2) ?></td>
  <td>₱<?= number_format($profit,2) ?></td>
  <td><?= $row['date_of_sale'] ?></td>
</tr>
<?php endwhile; ?>
</table>

<h3 style="text-align:right">
  Grand Total: ₱<?= number_format($grand_total,2) ?><br>
  Profit: ₱<?= number_format($profit_total,2) ?>
</h3>

</div>
</body>
</html>
