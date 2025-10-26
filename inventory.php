<?php include 'db.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>JB Builders</title>
    <style>
        /* === Global Styles === */
        body {
          font-family: "Poppins", Arial, sans-serif;
          margin: 0;
          padding: 0;
          background-color: #f4f6f8;
          color: #333;
        }

        /* === Page Container === */
        .container {
          width: 90%;
          max-width: 1100px;
          margin: 40px auto;
          background: #fff;
          padding: 30px;
          border-radius: 12px;
          box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        /* === Header === */
        h2 {
          text-align: center;
          color: #007bff;
          font-size: 2em;
          margin-bottom: 20px;
        }

        h3 {
          margin-top: 40px;
          color: #444;
        }

        /* === Buttons === */
        button {
          background: #007bff;
          border: none;
          color: white;
          padding: 8px 14px;
          border-radius: 6px;
          cursor: pointer;
          transition: 0.3s;
          font-weight: bold;
        }

        button:hover {
          background: #0056b3;
        }

        a button {
          margin-right: 6px;
        }

        a {
          text-decoration: none;
        }

        /* === Form === */
        form {
          margin-bottom: 20px;
          display: flex;
          flex-wrap: wrap;
          gap: 10px;
          align-items: center;
        }

        input[type="text"],
        input[type="number"] {
          padding: 8px;
          border-radius: 6px;
          border: 1px solid #ccc;
          width: 160px;
          transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="number"]:focus {
          border-color: #007bff;
          outline: none;
        }

        /* === Table === */
        table {
          border-collapse: collapse;
          width: 100%;
          margin-top: 15px;
        }

        th, td {
          border: 1px solid #ddd;
          padding: 10px;
          text-align: center;
        }

        th {
          background: #007bff;
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

        /* === Responsive Design === */
        @media (max-width: 768px) {
          .container {
            width: 95%;
            padding: 15px;
          }

          form {
            flex-direction: column;
            align-items: stretch;
          }

          input, button {
            width: 100%;
          }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>San Francisco Inventory System</h2>

    <!-- Navigation Buttons -->
    <div style="margin-bottom: 20px;">
        <a href="sales_report.php"><button type="button">View Sales Report</button></a>
        <a href="cashier.php"><button type="button">To Cashier</button></a>
    </div>

    <!-- Add Item Form -->
    <form method="POST" action="">
        <input type="text" name="item_code" placeholder="Code" required>
        <input type="text" name="item_name" placeholder="Name" required>
        <input type="number" name="quantity" placeholder="Quantity" required>
        <input type="number" step="0.01" name="unit_cost" placeholder="Unit Cost" required>
        <input type="number" step="0.01" name="srp" placeholder="SRP" required>
        <button type="submit" name="add">Add Item</button>
    </form>

    <?php
    if (isset($_POST['add'])) {
        $code = $_POST['item_code'];
        $name = $_POST['item_name'];
        $qty  = $_POST['quantity'];
        $uc   = $_POST['unit_cost'];
        $srp  = $_POST['srp'];

        // Check if item already exists
        $check = $conn->query("SELECT * FROM inventory WHERE item_code='$code'");
        if ($check->num_rows > 0) {
            // Update if exists
            $conn->query("UPDATE inventory 
                          SET quantity = quantity + $qty,
                              item_name = '$name',
                              unit_cost = $uc,
                              srp = $srp
                          WHERE item_code='$code'");
        } else {
            // Insert new
            $sql = "INSERT INTO inventory (item_code, item_name, quantity, unit_cost, srp)
                    VALUES ('$code', '$name', '$qty', '$uc', '$srp')";
            $conn->query($sql);
        }

        // Prevent form resubmission
        header("Location: index.php");
        exit();
    }
    ?>

    <h3>Inventory List</h3>
    <table>
        <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Quantity</th>
            <th>Unit Cost</th>
            <th>SRP</th>
            <th>Action</th>
        </tr>
        <?php
        $result = $conn->query("SELECT * FROM inventory");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['item_code']}</td>
                    <td>{$row['item_name']}</td>
                    <td>{$row['quantity']}</td>
                    <td>{$row['unit_cost']}</td>
                    <td>{$row['srp']}</td>
                    <td>
                        <form method='POST' action='sale.php' style='display:inline;'>
                            <input type='hidden' name='item_id' value='{$row['id']}'>
                            <input type='number' name='qty_sold' placeholder='Qty' required style='width:70px;'>
                            <button type='submit' name='sell'>Sell</button>
                        </form>
                        <a href='update.php?id={$row['id']}'><button type='button'>Update</button></a>
                        <a href='delete.php?id={$row['id']}' onclick=\"return confirm('Are you sure you want to delete this item?');\">
                            <button type='button'>Delete</button>
                        </a>
                    </td>
                  </tr>";
        }
        ?>
    </table>
</div>

</body>
</html>
