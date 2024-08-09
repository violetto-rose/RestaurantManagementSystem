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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['place_order'])) {
    $reservationOrderId = $_POST['reservation_order_id'] ?? null;
    $selectedMeals = $_POST['meals'] ?? [];
    $quantities = $_POST['quantities'] ?? [];
    $totalAmount = 0;

    $conn->begin_transaction();

    try {
        // Insert into Order
        $stmt = $conn->prepare("INSERT INTO `Order` (ReservationID, OrderDate, OrderTime) VALUES (?, CURDATE(), CURTIME())");
        if ($reservationOrderId === "") {
            $stmt->bind_param("s", $null);
        } else {
            $stmt->bind_param("i", $reservationOrderId);
        }
        $stmt->execute();
        $orderId = $stmt->insert_id;
        $stmt->close();

        // Insert into OrderItem
        $stmt = $conn->prepare("INSERT INTO OrderItem (OrderID, MenuID, Quantity) VALUES (?, ?, ?)");
        foreach ($selectedMeals as $index => $menuId) {
            $quantity = $quantities[$index] ?? 0;
            if ($quantity < 1) {
                continue;
            }

            // Fetch menu price and validate
            $priceStmt = $conn->prepare("SELECT Price FROM Menu WHERE MenuID = ?");
            $priceStmt->bind_param("i", $menuId);
            $priceStmt->execute();
            $priceStmt->bind_result($price);
            $priceStmt->fetch();
            $priceStmt->close();

            if ($price === null) {
                throw new Exception("Menu data is missing for MenuID $menuId.");
            }

            $stmt->bind_param("iii", $orderId, $menuId, $quantity);
            $stmt->execute();

            $totalAmount += $price * $quantity;
        }
        $stmt->close();

        $conn->commit();

        // Generate Bill
        $redirectUrl = "generate_bill.php?order_id=$orderId&total_amount=$totalAmount";
        if ($reservationOrderId !== "") {
            $redirectUrl .= "&reservation_id=$reservationOrderId";
        }
        header("Location: $redirectUrl");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $errorMessage = "Failed to place order: " . $e->getMessage();
    }
}

// Fetch menu items
$menuItems = $conn->query("SELECT * FROM Menu");

// Fetch reservations
$reservations = $conn->query("SELECT ReservationID, CustomerID, ReservationDate, ReservationTime FROM Reservation");

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
    <title>Billing - Swaad Sanchalan</title>
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
            font-family: 'Dancing Script', cursive;
            color: #492c07;
        }

        .btn-primary {
            background-color: #492c07;
            border-color: #492c07;
        }

        .btn-primary:hover {
            background-color: #ff6347;
            border-color: #ff6347;
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
    <?php $currentPage = 'billing.php'; include 'admin_header.php'; ?>

    <div class="billing-container">
        <h2 class="text-center">Place Order</h2>

        <?php if (isset($errorMessage)): ?>
            <div class="alert alert-danger text-center">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="reservation_order_id">Reservation Order ID (Optional)</label>
                <select class="form-control" id="reservation_order_id" name="reservation_order_id">
                    <option value="">No Reservation</option>
                    <?php while ($reservation = $reservations->fetch_assoc()): ?>
                        <option value="<?php echo $reservation['ReservationID']; ?>">
                            Reservation ID: <?php echo $reservation['ReservationID']; ?> - Customer ID:
                            <?php echo $reservation['CustomerID']; ?> - Date: <?php echo $reservation['ReservationDate']; ?>
                            - Time: <?php echo $reservation['ReservationTime']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="meal_search">Search Meals</label>
                <input type="text" class="form-control" id="meal_search" placeholder="Search for meals...">
            </div>

            <div class="form-group">
                <label for="meal_list">Select Meals</label>
                <div id="meal_list" class="form-group">
                    <?php while ($row = $menuItems->fetch_assoc()): ?>
                        <?php if (!empty($row['ItemName']) && !empty($row['Price'])): ?>
                            <div class="form-group">
                                <label>
                                    <?php echo htmlspecialchars($row['ItemName']); ?> -
                                    ₹<?php echo number_format($row['Price'], 2); ?>
                                </label>
                                <input type="number" class="form-control mt-1" name="quantities[]" placeholder="Quantity"
                                    min="1">
                                <input type="hidden" name="meals[]" value="<?php echo $row['MenuID']; ?>">
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                Menu data is missing expected fields for item ID <?php echo htmlspecialchars($row['MenuID']); ?>
                            </div>
                        <?php endif; ?>
                    <?php endwhile; ?>
                </div>
            </div>

            <button type="submit" name="place_order" class="btn btn-primary btn-block">Place Order</button>
        </form>
    </div>

    <?php include 'admin_footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.getElementById('meal_search').addEventListener('input', function () {
            var searchValue = this.value.toLowerCase();
            var mealList = document.getElementById('meal_list');
            var meals = mealList.getElementsByClassName('form-group');

            Array.prototype.forEach.call(meals, function (meal) {
                var label = meal.getElementsByTagName('label')[0];
                if (label.innerText.toLowerCase().includes(searchValue)) {
                    meal.style.display = '';
                } else {
                    meal.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>