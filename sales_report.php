<?php include 'db.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Sales Report - JB Builders</title>
    <style>
        /* === Global === */
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

        .back-btn {
            background: #6c757d;
            border: none;
            color: white;
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }

        .back-btn:hover {
            background: #5a6268;
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
            letter-spacing: 0.5px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #eef5ff;
        }

        h3 {
            text-align: right;
            color: #333;
            margin-top: 25px;
        }

        /* === Responsive === */
        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 20px;
            }

            table, th, td {
                font-size: 13px;
            }

            h3 {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Sales Report</h2>
    <a href="inventory.php"><button class="back-btn">⬅ Back to Inventory</button></a>

    <table>
        <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Quantity Sold</th>
            <th>Unit Cost</th>
            <th>SRP</th>
            <th>Total Sales</th>
            <th>Date of Sale</th>
        </tr>

        <?php
        $result = $conn->query("
            SELECT s.id, i.item_code, i.item_name, s.quantity_sold, 
                   i.unit_cost, i.srp, s.date_of_sale
            FROM sales s
            JOIN inventory i ON s.item_id = i.id
            ORDER BY s.date_of_sale DESC
        ");

        $grand_total = 0;

        while ($row = $result->fetch_assoc()) {
            $total_sales = $row['srp'] * $row['quantity_sold'];
            $grand_total += $total_sales;

            echo "<tr>
                    <td>{$row['item_code']}</td>
                    <td>{$row['item_name']}</td>
                    <td>{$row['quantity_sold']}</td>
                    <td>₱{$row['unit_cost']}</td>
                    <td>₱{$row['srp']}</td>
                    <td><b>₱" . number_format($total_sales, 2) . "</b></td>
                    <td>{$row['date_of_sale']}</td>
                  </tr>";
        }
        ?>
    </table>

    <h3>Grand Total: ₱<?php echo number_format($grand_total, 2); ?></h3>
</div>

</body>
</html>
