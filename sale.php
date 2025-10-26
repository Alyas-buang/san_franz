<?php include 'db.php'; ?>

<?php
if (isset($_POST['sell'])) {
    $item_id   = $_POST['item_id'];
    $qty_sold  = $_POST['qty_sold'];
    $date_sale = date("Y-m-d");

    // Check available stock
    $res = $conn->query("SELECT quantity FROM inventory WHERE id=$item_id");
    $row = $res->fetch_assoc();

    if ($row['quantity'] >= $qty_sold) {
        // Deduct from inventory
        $conn->query("UPDATE inventory SET quantity = quantity - $qty_sold WHERE id=$item_id");

        // Record the sale
        $conn->query("INSERT INTO sales (item_id, quantity_sold, date_of_sale) 
                      VALUES ($item_id, $qty_sold, '$date_sale')");

        echo "Sale recorded successfully. <a href='index.php'>Go back</a>";
    } else {
        echo "Not enough stock. <a href='index.php'>Go back</a>";
    }
}
?>
