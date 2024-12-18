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
        <img src="https://scontent.fmnl8-1.fna.fbcdn.net/v/t39.30808-6/279800257_484698290119133_1493035390163788707_n.jpg?_nc_cat=108&ccb=1-7&_nc_sid=6ee11a&_nc_eui2=AeEN5D3IHhLGGVQpK1w4dln89Dc0gcH2VYr0NzSBwfZViqLQWGDZdFCQ0nfgeLRk_h74OMxg5TfPpntUltBzTq-i&_nc_ohc=Eg1BcmoPvjIQ7kNvgFqjdX8&_nc_zt=23&_nc_ht=scontent.fmnl8-1.fna&_nc_gid=ASN7breEqg_86J-2TFEBqVg&oh=00_AYC_M5SgFXCZflKQFCe_GrcQ8D_91TvV8HAb6WbBz_OLdw&oe=676882BA" alt="Logo" class="logo">
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
