<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

// Initialize variables
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Store date range in session if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['startDate'] = $_POST['startDate'];
    $_SESSION['endDate'] = $_POST['endDate'];
}

// Retrieve date range from session
$startDate = isset($_SESSION['startDate']) ? $_SESSION['startDate'] : '';
$endDate = isset($_SESSION['endDate']) ? $_SESSION['endDate'] : '';

$showReport = !empty($startDate) && !empty($endDate);

function fetchData($conn, $sql, $params = [])
{
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $data;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Swaad Sanchalan is a comprehensive restaurant management system offering menu management, reservations, billing, and sales reporting.">
    <meta name="keywords"
        content="Restaurant Management, Swaad Sanchalan, Menu Management, Reservations, Billing, Sales Reporting">
    <meta name="author" content="Manju Madhav V A, Nishant K R">
    <title>Sales Report</title>
    <link rel="icon" href="IMG/favicon.ico" type="image/x-icon">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        .container {
            flex: 1;
        }

        .container {
            flex: 1;
        }

        .pagination .page-link {
            color: black;
        }

        .pagination .page-link:hover {
            color: black;
        }

        .pagination .page-item.active .page-link {
            background-color: black;
            border-color: black;
        }
    </style>
</head>

<body>
    <?php include 'admin_header.php'; ?>
    <div class="container mt-5">
        <h2 class="text-center">Sales Report</h2>

        <form method="post" class="mb-4">
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="startDate">Start Date</label>
                    <input type="date" class="form-control" id="startDate" name="startDate"
                        value="<?php echo $startDate; ?>" required>
                </div>
                <div class="form-group col-md-3">
                    <label for="endDate">End Date</label>
                    <input type="date" class="form-control" id="endDate" name="endDate" value="<?php echo $endDate; ?>"
                        required>
                </div>
                <div class="form-group col-md-3">
                    <button type="submit" class="btn btn-primary mt-4">Generate Report</button>
                </div>
            </div>
        </form>


        <?php
        if ($showReport) {
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "SwaadSanchalan";

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Count total rows
            $countSql = "SELECT COUNT(*) as total FROM (
                SELECT DATE(o.OrderDate) AS Date
                FROM `Order` o
                WHERE o.OrderDate BETWEEN ? AND ?
                GROUP BY DATE(o.OrderDate)
            ) as subquery";

            $totalRows = fetchData($conn, $countSql, [$startDate, $endDate])[0]['total'];
            $totalPages = ceil($totalRows / $perPage);

            // Fetch paginated data for table
            $tableSql = "
            SELECT 
                DATE(o.OrderDate) AS Date,
                COUNT(o.OrderID) AS TotalOrders,
                COALESCE(SUM(oi.Quantity * m.Price), 0) AS TotalSales
            FROM `Order` o
            LEFT JOIN OrderItem oi ON o.OrderID = oi.OrderID
            LEFT JOIN Menu m ON oi.MenuID = m.MenuID
            WHERE o.OrderDate BETWEEN ? AND ?
            GROUP BY DATE(o.OrderDate)
            ORDER BY Date ASC
            LIMIT ?, ?
            ";

            $tableData = fetchData($conn, $tableSql, [$startDate, $endDate, $offset, $perPage]);

            // Fetch all data for graph
            $graphSql = "
            SELECT 
                DATE(o.OrderDate) AS Date,
                COUNT(o.OrderID) AS TotalOrders,
                COALESCE(SUM(oi.Quantity * m.Price), 0) AS TotalSales
            FROM `Order` o
            LEFT JOIN OrderItem oi ON o.OrderID = oi.OrderID
            LEFT JOIN Menu m ON oi.MenuID = m.MenuID
            WHERE o.OrderDate BETWEEN ? AND ?
            GROUP BY DATE(o.OrderDate)
            ORDER BY Date ASC
            ";

            $graphData = fetchData($conn, $graphSql, [$startDate, $endDate]);

            if (!empty($tableData)) {
                echo "<h3 class='mt-4'>Sales Report from " . date('d/m/Y', strtotime($startDate)) . " to " . date('d/m/Y', strtotime($endDate)) . "</h3>";
                echo "<table class='table table-bordered' id='salesTable'>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Total Sales</th>
                                <th>Total Orders</th>
                            </tr>
                        </thead>
                        <tbody>";

                foreach ($tableData as $row) {
                    $formattedDate = date('d/m/Y', strtotime($row["Date"]));
                    echo "<tr>
                            <td>" . $formattedDate . "</td>
                            <td>₹" . number_format($row["TotalSales"], 2) . "</td>
                            <td>" . $row["TotalOrders"] . "</td>
                        </tr>";
                }

                echo "</tbody></table>";

                // Pagination controls
                echo "<nav aria-label='Page navigation'>";
                echo "<ul class='pagination justify-content-center'>";
                for ($i = 1; $i <= $totalPages; $i++) {
                    echo "<li class='page-item " . ($page == $i ? 'active' : '') . "'><a class='page-link' href='?page=$i'>$i</a></li>";
                }
                echo "</ul>";
                echo "</nav>";

                echo "<canvas id='salesChart' style='width: 100%; height: 400px;'></canvas>";

                echo "<form method='post' action='export.php' class='mt-4'>
                        <input type='hidden' name='startDate' value='$startDate'>
                        <input type='hidden' name='endDate' value='$endDate'>
                        <button type='submit' class='btn btn-secondary'>Export to CSV</button>
                    </form>";
            } else {
                echo "<div class='alert alert-info text-center'>No sales data available for the selected date range.</div>";
            }

            $conn->close();
        }
        ?>

    </div>

    <?php include 'admin_footer.php'; ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var ctx = document.getElementById('salesChart');
            if (ctx) {
                var graphData = <?php echo json_encode($graphData ?? []); ?>;
                var dates = graphData.map(item => item.Date);
                var sales = graphData.map(item => item.TotalSales);
                var orders = graphData.map(item => item.TotalOrders);

                var salesChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: dates,
                        datasets: [
                            {
                                label: 'Total Sales (₹)',
                                data: sales,
                                borderColor: 'rgba(75, 192, 192, 1)',
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                yAxisID: 'y-axis-1'
                            },
                            {
                                label: 'Total Orders',
                                data: orders,
                                borderColor: 'rgba(153, 102, 255, 1)',
                                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                                yAxisID: 'y-axis-2'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Date'
                                }
                            },
                            'y-axis-1': {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Total Sales (₹)'
                                }
                            },
                            'y-axis-2': {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'Total Orders'
                                },
                                grid: {
                                    drawOnChartArea: false
                                }
                            }
                        }
                    }
                });
            } else {
                console.error('Canvas element not found');
            }
        });
    </script>
</body>

</html>