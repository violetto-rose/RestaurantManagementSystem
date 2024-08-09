<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "swaadsanchalan";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];

    // Query to get sales report
    $sql = "
    SELECT 
        o.OrderDate AS Date,
        SUM(oi.Quantity * m.Price) AS TotalSales,
        COUNT(o.OrderID) AS TotalOrders
    FROM `Order` o
    JOIN OrderItem oi ON o.OrderID = oi.OrderID
    JOIN Menu m ON oi.MenuID = m.MenuID
    WHERE o.OrderDate BETWEEN ? AND ?
    GROUP BY o.OrderDate
    ORDER BY o.OrderDate DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();

    // Open output stream for CSV
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=sales_report_' . date('Ymd') . '.csv');
    $output = fopen('php://output', 'w');

    // Output header
    fputcsv($output, ['Date', 'Total Sales', 'Total Orders']);

    // Output data
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['Date'],
            '$' . number_format($row['TotalSales'], 2),
            $row['TotalOrders']
        ]);
    }

    fclose($output);
    $stmt->close();
    $conn->close();
    exit();
}