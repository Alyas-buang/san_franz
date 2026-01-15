<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<?php

include 'db.php';

// Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add to cart
if (isset($_POST['add_to_cart'])) {
    $item_id = $_POST['item_id'];
    $qty = $_POST['qty'];

    $res = $conn->query("SELECT * FROM inventory WHERE id=$item_id");
    $item = $res->fetch_assoc();

    if ($item && $item['quantity'] >= $qty) {
        $found = false;
        foreach ($_SESSION['cart'] as &$cart_item) {
            if ($cart_item['id'] == $item_id) {
                $cart_item['qty'] += $qty;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $_SESSION['cart'][] = [
                'id' => $item['id'],
                'code' => $item['item_code'],
                'name' => $item['item_name'],
                'srp' => $item['srp'],
                'qty' => $qty
            ];
        }
    } else {
        echo "<p style='color:red;'>❌ Not enough stock for {$item['item_name']}</p>";
    }
}

// Checkout
if (isset($_POST['checkout'])) {
    $date_sale = date("Y-m-d");
    foreach ($_SESSION['cart'] as $cart_item) {
        $item_id = $cart_item['id'];
        $qty_sold = $cart_item['qty'];
        $conn->query("UPDATE inventory SET quantity = quantity - $qty_sold WHERE id=$item_id");
        $conn->query("INSERT INTO sales (item_id, quantity_sold, date_of_sale)
                      VALUES ($item_id, $qty_sold, '$date_sale')");
    }
    $_SESSION['cart'] = [];
    echo "<p style='color:green;'>✅ Checkout completed successfully!</p>";
}

// Remove item from cart
if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    $_SESSION['cart'] = array_filter($_SESSION['cart'], fn($item) => $item['id'] != $remove_id);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cashiering - JB Builders</title>
    <style>
        /* === Global Style === */
        body {
            font-family: "Poppins", Arial, sans-serif;
            background: #f4f6f8;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 1100px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }

        a {
            text-decoration: none;
            color: white;
        }

        .nav-buttons {
            margin-bottom: 15px;
        }

        .btn {
            background: #007bff;
            border: none;
            color: white;
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            margin-right: 5px;
            transition: 0.3s;
        }

        .btn:hover {
            background: #0056b3;
        }

        .btn-gray {
            background: #6c757d;
        }

        .btn-gray:hover {
            background: #5a6268;
        }

        /* === Form === */
        form {
            margin-top: 15px;
        }

        select, input {
            padding: 8px;
            margin: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        /* === Table === */
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: white;
            text-transform: uppercase;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #eef5ff;
        }

        .total {
            text-align: right;
            margin-top: 15px;
            font-weight: bold;
            font-size: 18px;
        }

        @media (max-width: 768px) {
            .container { width: 95%; padding: 20px; }
            table, th, td { font-size: 13px; }
            .btn { font-size: 13px; padding: 6px 10px; }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Cashiering - JB Builders</h2>

    <div class="nav-buttons">
        <a href="inventory.php"><button class="btn btn-gray">⬅ Back to Inventory</button></a>
        <a href="sales_report.php"><button class="btn">📊 Sales Report</button></a>
    </div>

    <!-- Add Item Form -->
    <form method="POST" action="">
        <label>Item:</label>
        <select name="item_id" required>
            <option value="">-- Select Item --</option>
            <?php
            $res = $conn->query("SELECT * FROM inventory WHERE quantity > 0");
            while ($row = $res->fetch_assoc()) {
                echo "<option value='{$row['id']}'>{$row['item_code']} - {$row['item_name']} (₱{$row['srp']} | Stock: {$row['quantity']})</option>";
            }
            ?>
        </select>

        <label>Quantity:</label>
        <input type="number" name="qty" min="1" required>

        <button type="submit" name="add_to_cart" class="btn">Add to Cart</button>
    </form>

    <!-- Cart Table -->
    <h3>🛒 Cart</h3>
    <table>
        <tr>
            <th>Code</th>
            <th>Name</th>
            <th>SRP</th>
            <th>Quantity</th>
            <th>Total</th>
            <th>Action</th>
        </tr>
        <?php
        $grand_total = 0;
        if (!empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $cart_item) {
                $total = $cart_item['srp'] * $cart_item['qty'];
                $grand_total += $total;
                echo "<tr>
                        <td>{$cart_item['code']}</td>
                        <td>{$cart_item['name']}</td>
                        <td>₱" . number_format($cart_item['srp'], 2) . "</td>
                        <td>{$cart_item['qty']}</td>
                        <td><b>₱" . number_format($total, 2) . "</b></td>
                        <td><a href='cashier.php?remove={$cart_item['id']}'><button type='button' class='btn btn-gray'>Remove</button></a></td>
                      </tr>";
            }
            echo "<tr>
                    <td colspan='4'><b>Grand Total</b></td>
                    <td colspan='2'><b>₱" . number_format($grand_total, 2) . "</b></td>
                  </tr>";
        } else {
            echo "<tr><td colspan='6'>🛒 Cart is empty</td></tr>";
        }
        ?>
    </table>

    <!-- Checkout -->
    <?php if (!empty($_SESSION['cart'])): ?>
        <form method="POST" action="">
            <button type="submit" name="checkout" class="btn" style="margin-top:15px;">Finalize Checkout</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
