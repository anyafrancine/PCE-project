<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Branch Performance Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0;padding: 0;background: #f4f4f9; }

        .header-container { display: flex;  align-items: center; background-color: #454d32; padding: 20px 40px; color: white;}

        .logo { width: 200px; height: auto; margin-right: 20px; }

        .text-container { text-align: left; flex-grow: 1; }

        .text-container h1 { font-size: 85px; margin: 0; }

        .text-container h2 { font-size: 40px; margin: 5px 0;}

        table { width: 85%; margin: 10px auto;border-collapse: collapse; background: #ffffff; }

        th, td { padding: 15px; font-size: 25px; text-align: center; border: 1px solid #ddd; }

        th { background-color: #454d32; color: white; }

        tr:nth-child(even) { background-color: #f2f2f2; }

        tr:hover { background-color: #ddd; }
    </style>
</head>
<body>
    <div class="header-container">
        <img src="https://scontent.fmnl8-1.fna.fbcdn.net/v/t39.30808-6/279800257_484698290119133_1493035390163788707_n.jpg?_nc_cat=108&ccb=1-7&_nc_sid=6ee11a&_nc_eui2=AeEN5D3IHhLGGVQpK1w4dln89Dc0gcH2VYr0NzSBwfZViqLQWGDZdFCQ0nfgeLRk_h74OMxg5TfPpntUltBzTq-i&_nc_ohc=MKRkUm7wqIcQ7kNvgGhtNx9&_nc_zt=23&_nc_ht=scontent.fmnl8-1.fna&_nc_gid=A50J6zq1t4dFQWVKge8rDj9&oh=00_AYDF1sNsc8M8KwT9iyIanp4XLOtOUt8uj7zeseu_0mvd2Q&oe=675F483A" alt="Logo" class="logo">
        <div class="text-container">
            <h1>Olanis Group of Companies, Inc.</h1>
            <h2>Branch Performance Report</h2>
        </div>
    </div>
    
    <hr>
    <table>
        <tr>
            <th>Branch Name</th>
            <th>Total Sales</th>
            <th>Total Transactions</th>
            <th>Average Sale Value</th>
            <th>Number of Products Sold</th>
        </tr>
        <?php
        $servername = "localhost";
        $username = "root";
        $password = "password";
        $dbname = "Olanis";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "
            SELECT 
                branches.branch_name,
                SUM(sales.total_price) AS total_sales,
                COUNT(sales.sale_id) AS total_transactions,
                AVG(sales.total_price) AS avg_sale_value,
                SUM(sales.quantity) AS products_sold
            FROM 
                sales
            INNER JOIN 
                branches ON sales.branch_id = branches.branch_id
            GROUP BY 
                branches.branch_name
            ORDER BY 
                total_sales DESC;
        ";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['branch_name']}</td>
                    <td>{$row['total_sales']}</td>
                    <td>{$row['total_transactions']}</td>
                    <td>{$row['avg_sale_value']}</td>
                    <td>{$row['products_sold']}</td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No data available</td></tr>";
        }
        $conn->close();
        ?>
    </table>
</body>
</html>
