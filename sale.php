<?php
include 'db.php';

if (!isset($_POST['sell'])) {
    header("Location: inventory.php");
    exit;
}

$item_id  = (int)$_POST['item_id'];
$qty_sold = (int)$_POST['qty_sold'];
$date     = date("Y-m-d");

if ($qty_sold <= 0) {
    die("Invalid quantity. <a href='inventory.php'>Go back</a>");
}

$conn->begin_transaction();

try {
    // Get current stock
    $stmt = $conn->prepare("SELECT quantity FROM inventory WHERE id = ? FOR UPDATE");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Item not found.");
    }

    $row = $result->fetch_assoc();

    if ($row['quantity'] < $qty_sold) {
        throw new Exception("Not enough stock.");
    }

    // Deduct stock
    $update = $conn->prepare(
        "UPDATE inventory SET quantity = quantity - ? WHERE id = ?"
    );
    $update->bind_param("ii", $qty_sold, $item_id);
    $update->execute();

    // Insert sale record
    $insert = $conn->prepare(
        "INSERT INTO sales (item_id, quantity_sold, date_of_sale)
         VALUES (?, ?, ?)"
    );
    $insert->bind_param("iis", $item_id, $qty_sold, $date);
    $insert->execute();

    $conn->commit();

    header("Location: inventory.php?success=sold");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    echo $e->getMessage() . " <a href='inventory.php'>Go back</a>";
}
