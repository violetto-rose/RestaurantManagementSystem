<?php
$reservationMessage = '';
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "swaadsanchalan";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customerID = $_POST['customerID'] ?? '';

    if (!empty($customerID)) {
        $stmt = $conn->prepare("SELECT * FROM Reservation WHERE CustomerID = ?");
        $stmt->bind_param("i", $customerID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $reservation = $result->fetch_assoc();

            // Convert and format the date
            $date = new DateTime($reservation['ReservationDate']);
            $formattedDate = $date->format('d/m/Y');

            // Convert and format the time
            $time = new DateTime($reservation['ReservationTime']);
            $formattedTime = $time->format('h:i A');

            $reservationMessage = "<div class='alert alert-info'>
                Reservation Details:<br>
                Table ID: " . htmlspecialchars($reservation['TableID']) . "<br>
                Date: " . htmlspecialchars($formattedDate) . "<br>
                Time: " . htmlspecialchars($formattedTime) . "<br>
                Number of Guests: " . htmlspecialchars($reservation['NumberOfGuests']) . "<br>
                Table Type: " . htmlspecialchars($reservation['TableType']) . "<br>
                Price: ₹" . htmlspecialchars($reservation['Price']) . "
            </div>";
        } else {
            $reservationMessage = "<div class='alert alert-warning'>No reservation found for Customer ID: " . htmlspecialchars($customerID) . "</div>";
        }
        $stmt->close();
    } else {
        $reservationMessage = "<div class='alert alert-danger'>Please enter a Customer ID.</div>";
    }
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
    <title>Check Reservation</title>
    <link rel="icon" href="IMG/favicon.ico" type="image/x-icon">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="CSS/styles.css" rel="stylesheet">
</head>

<body>
    <div class="wrapper">
        <?php $currentPage = 'check_reservation.php';
        include 'header.php'; ?>

        <div class="content">
            <div class="container mt-5">
                <h2 class="text-center">Check Your Reservation</h2>
                <form id="checkReservationForm" method="post" action="">
                    <div class="form-group">
                        <label for="customerID">Customer ID</label>
                        <input type="text" class="form-control form-control-lg" id="customerID" name="customerID"
                            placeholder="Enter your Customer ID" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg btn-block">Check Reservation</button>
                </form>

                <?php
                if (!empty($reservationMessage)) {
                    echo $reservationMessage;
                }
                ?>

                <!-- Cancel Reservation Button -->
                <form id="cancelReservationForm" method="post" action="cancel_reservation.php" class="mt-3">
                    <input type="hidden" name="customerID"
                        value="<?php echo htmlspecialchars($_POST['customerID'] ?? ''); ?>">
                    <button type="submit" class="btn btn-danger btn-lg btn-block">Cancel Reservation</button>
                </form>
            </div>
        </div>

        <?php include 'footer.php'; ?>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>