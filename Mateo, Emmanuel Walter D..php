<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Branch Performance Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f4f4f9; }
        .header-container { display: flex; align-items: center; background-color: #454d32; padding: 20px 40px; color: white; }
        .logo { width: 200px; margin-right: 20px; }
        .text-container h1 { font-size: 85px; margin: 0; }
        .text-container h2 { font-size: 40px; margin: 5px 0; }
        table { width: 85%; margin: 0 auto; border-collapse: collapse; background: #ffffff; }
        th, td { padding: 15px; font-size: 35px; text-align: center; border: 2px solid #454d32; }
        th { background-color: #454d32; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        tr:hover { background-color: #ddd; }
        h3 { text-align: center; font-size: 30px; margin: 40px auto 0; background-color: #454d32; padding: 20px 40px; color: white; width: 85%; box-sizing: border-box; border: 2px solid #f2f2f2; }
        hr { margin: 20px 0; }
    </style>
</head>
<body>
    <div class="header-container">
        <img src="olanis_logo.png" alt="Logo" class="logo">
        <div class="text-container">
            <h1>Olanis Group of Companies, Inc.</h1>
            <h2>Branch Performance Report</h2>
        </div>
    </div>

    <?php
    $servername = "localhost";
    $username = "root";
    $password = "password";
    $dbname = "Olanis";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query to fetch detailed sales data
    $sql_sales_details = "
        SELECT 
            b.branch_name,
            s.sale_date,
            t.tile_name,
            c.category_name,
            t.color,
            t.material,
            t.tile_size,
            s.quantity,
            t.price as price_per_tile,
            s.quantity * t.price as total_payment,
            cu.customer_name
        FROM sales s
        INNER JOIN tiles t ON s.tile_id = t.tile_id
        INNER JOIN categories c ON t.category_id = c.category_id
        INNER JOIN branches b ON s.branch_id = b.branch_id
        INNER JOIN customers cu ON s.customer_id = cu.customer_id
        WHERE MONTH(s.sale_date) = MONTH(CURRENT_DATE()) 
        AND YEAR(s.sale_date) = YEAR(CURRENT_DATE())
        ORDER BY b.branch_name, s.sale_date;
    ";

    $sql_total_sales = "
        SELECT 
            b.branch_name,
            SUM(s.total_price) AS total_sales
        FROM sales s
        INNER JOIN branches b ON s.branch_id = b.branch_id
        WHERE MONTH(s.sale_date) = MONTH(CURRENT_DATE()) 
        AND YEAR(s.sale_date) = YEAR(CURRENT_DATE())
        GROUP BY b.branch_name
        ORDER BY total_sales DESC;
    ";

    echo "<h2 style='text-align: center; font-size: 50px; margin: 0; color: #454d32;'>Sales Report for " . date('F Y') . "</h2><hr style='margin: 0'>";


    $result_details = $conn->query($sql_sales_details);
    $current_branch = null;

    if ($result_details->num_rows > 0) {
        while ($row = $result_details->fetch_assoc()) {
            if ($current_branch !== $row['branch_name']) {
                if ($current_branch !== null) echo "</table>";
                echo "<h3>{$row['branch_name']} Sales</h3>";
                echo "<table>
                        <tr>
                            <th>Date</th>
                            <th>Tile Name</th>
                            <th>Category</th>
                            <th>Color</th>
                            <th>Material</th>
                            <th>Tile Size</th>
                            <th>Quantity</th>
                            <th>Price Per Piece</th>
                            <th>Total Price Paid</th>
                            <th>Customer Name</th>
                        </tr>";
                $current_branch = $row['branch_name'];
            }
            echo "<tr>
                    <td>{$row['sale_date']}</td>
                    <td>{$row['tile_name']}</td>
                    <td>{$row['category_name']}</td>
                    <td>{$row['color']}</td>
                    <td>{$row['material']}</td>
                    <td>{$row['tile_size']}</td>
                    <td>{$row['quantity']}</td>
                    <td>{$row['price_per_tile']}</td>
                    <td>{$row['quantity']}</td>
                    <td>{$row['customer_name']}</td>
                  </tr>";
        }
        echo "</table>";
    }

    $result_totals = $conn->query($sql_total_sales);
    echo "<h3>Overall Total Sales</h3>";
    echo "<table><tr><th>Branch Name</th><th>Total Sales</th></tr>";
    $total_sales_overall = 0;
    while ($row = $result_totals->fetch_assoc()) {
        $total_sales_overall += $row['total_sales'];
        echo "<tr><td>{$row['branch_name']}</td><td>" . number_format($row['total_sales'], 2) . "</td></tr>";
    }
    echo "<tr><th>Grand Total</th><th>" . number_format($total_sales_overall, 2) . "</th></tr></table>";

    $conn->close();
    ?>
</body>
</html>
