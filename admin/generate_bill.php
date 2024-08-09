<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "SwaadSanchalan";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

require ('../fpdf/fpdf.php');

class PDF extends FPDF
{
    function Header()
    {
        $this->Image('../IMG/logo.png', 55, 10, 100, 15.7);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(80);
        $this->Ln(20);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$orderId = $_GET['order_id'] ?? null;
$reservationId = $_GET['reservation_id'] ?? null;

if (!$orderId) {
    die("Order ID is required.");
}

$billDetails = [];
$orderItems = [];
$totalAmount = 0;
$reservationCharge = 0;

try {
    $conn->begin_transaction();

    // Fetch order details
    $stmt = $conn->prepare("SELECT ReservationID, OrderDate, OrderTime FROM `Order` WHERE OrderID = ?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($order = $result->fetch_assoc()) {
        $billDetails['OrderID'] = $orderId;
        $billDetails['ReservationID'] = $order['ReservationID'] ? $order['ReservationID'] : 'No Reservation';
        $billDetails['OrderDate'] = date('d/m/Y', strtotime($order['OrderDate']));
        $billDetails['OrderTime'] = $order['OrderTime'];
        $reservationId = $order['ReservationID'];
    } else {
        throw new Exception("Order not found.");
    }
    $stmt->close();

    // Fetch reservation details and charge
    if ($reservationId) {
        $stmt = $conn->prepare("SELECT Price FROM Reservation WHERE ReservationID = ?");
        $stmt->bind_param("i", $reservationId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($reservation = $result->fetch_assoc()) {
            $reservationCharge = $reservation['Price'];
        }
        $stmt->close();
    }

    // Fetch order items
    $stmt = $conn->prepare("SELECT Menu.ItemName, OrderItem.Quantity, Menu.Price, (Menu.Price * OrderItem.Quantity) AS Amount
                             FROM OrderItem
                             JOIN Menu ON OrderItem.MenuID = Menu.MenuID
                             WHERE OrderItem.OrderID = ?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $amount = floatval($row['Amount']);
        $orderItems[] = [
            'ItemName' => $row['ItemName'],
            'Quantity' => $row['Quantity'],
            'Price' => $row['Price'],
            'Amount' => $amount
        ];
        $totalAmount += $amount;
    }
    $stmt->close();

    // Add reservation charge to total amount
    $totalAmount += $reservationCharge;

    // Handle PDF export
    if (isset($_POST['export'])) {
        $pdf = new PDF();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Bill', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);

        $pdf->Cell(0, 10, 'Order ID: ' . $billDetails['OrderID'], 0, 1);
        $pdf->Cell(0, 10, 'Reservation ID: ' . $billDetails['ReservationID'], 0, 1);
        $pdf->Cell(0, 10, 'Date: ' . $billDetails['OrderDate'] . ' ' . $billDetails['OrderTime'], 0, 1);
        $pdf->Ln(10);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(80, 10, 'Item', 1);
        $pdf->Cell(25, 10, 'Quantity', 1);
        $pdf->Cell(40, 10, 'Price (Rs)', 1);
        $pdf->Cell(45, 10, 'Amount (Rs)', 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 12);
        foreach ($orderItems as $item) {
            $pdf->Cell(80, 10, $item['ItemName'], 1);
            $pdf->Cell(25, 10, $item['Quantity'], 1);
            $pdf->Cell(40, 10, number_format($item['Price'], 2), 1);
            $pdf->Cell(45, 10, number_format($item['Amount'], 2), 1);
            $pdf->Ln();
        }

        if ($reservationCharge > 0) {
            $pdf->Cell(145, 10, 'Reservation Charge', 1);
            $pdf->Cell(45, 10, number_format($reservationCharge, 2), 1);
            $pdf->Ln();
        }

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(145, 10, 'Total Amount', 1);
        $pdf->Cell(45, 10, 'Rs ' . number_format($totalAmount, 2), 1);

        $pdf->Output('D', 'bill.pdf');

        // Delete the reservation after generating the bill
        if ($reservationId) {
            $stmt = $conn->prepare("DELETE FROM Reservation WHERE ReservationID = ?");
            $stmt->bind_param("i", $reservationId);
            $stmt->execute();
            $stmt->close();
        }

        $conn->commit();
        exit();
    }

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    echo "<p class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

$conn->close();
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
    <title>Bill Details - Swaad Sanchalan</title>
    <link rel="icon" href="IMG/favicon.ico" type="image/x-icon">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap"
        rel="stylesheet">
    <style>
        body {
            background: linear-gradient(90deg, #ffc34e, #faf5aa);
            font-family: 'Poppins', sans-serif;
        }

        .navbar {
            background-color: #492c07;
            padding: 1rem;
        }

        .navbar-brand img {
            max-height: 100px;
            width: auto;
        }

        .billing-container {
            max-width: 800px;
            margin: 5% auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .billing-container h2 {
            font-family: 'Dancing Script';
            color: #492c07;
        }

        .btn {
            background-color: black;
            border-color: white;
            color: white;
        }

        .btn:hover,
        .btn:focus,
        .btn:active {
            color: black;
            background-color: #cb7d18;
            border-color: black;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }
    </style>
</head>

<body>
    <?php $currentPage = 'billing.php';
    include 'admin_header.php'; ?>

    <div class="billing-container">
        <h2 class="text-center">Bill Details</h2>

        <?php foreach ($billDetails as $key => $detail): ?>
            <p><strong><?php echo htmlspecialchars($key); ?>:</strong> <?php echo htmlspecialchars($detail); ?></p>
        <?php endforeach; ?>

        <?php if (!empty($orderItems)): ?>
            <h3>Order Items:</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Price (Rs)</th>
                        <th>Amount (Rs)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orderItems as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['ItemName']); ?></td>
                            <td><?php echo htmlspecialchars($item['Quantity']); ?></td>
                            <td><?php echo number_format($item['Price'], 2); ?></td>
                            <td><?php echo number_format($item['Amount'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($reservationCharge > 0): ?>
                        <tr>
                            <td colspan="3"><strong>Reservation Charge</strong></td>
                            <td><?php echo number_format($reservationCharge, 2); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No items found for this order.</p>
        <?php endif; ?>

        <h3 style="font-family:'Poppins';">Total Amount: Rs <?php echo number_format($totalAmount, 2); ?></h3>

        <form method="post" action="">
            <button type="submit" name="export" class="btn btn-primary btn-block">Export as PDF</button>
        </form>
    </div>

    <?php include 'admin_footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>