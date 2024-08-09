<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'SwaadSanchalan';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customerID = isset($_POST['customerID']) ? $_POST['customerID'] : '';
    $phone = isset($_POST['phone']) ? ltrim($_POST['phone'], '0') : '';

    if (!empty($customerID) && !empty($phone)) {
        $stmt = $conn->prepare("DELETE FROM Reservation WHERE CustomerID = ? AND CustomerID IN (SELECT CustomerID FROM Customer WHERE Phone = ?)");
        $stmt->bind_param("ss", $customerID, $phone);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $message = "<div class='alert alert-success'>Reservation canceled successfully.</div>";
            } else {
                $message = "<div class='alert alert-warning'>No reservation found for the provided details.</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>Error canceling reservation: " . $stmt->error . "</div>";
        }

        $stmt->close();
    } else {
        $message = "<div class='alert alert-warning'>Please provide both Customer ID and Phone Number.</div>";
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
    <title>Cancel Reservation</title>
    <link rel="icon" href="IMG/favicon.ico" type="image/x-icon">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="CSS/styles.css" rel="stylesheet">
</head>

<body>
    <div class="wrapper">
        <?php include 'header.php'; ?>

        <div class="content">
            <div class="container mt-5">
                <h2 class="text-center">Cancel Your Reservation</h2>
                <form id="cancelReservationForm" method="post" action="">
                    <div class="form-group">
                        <label for="customerID">Customer ID</label>
                        <input type="text" class="form-control form-control-lg" id="customerID" name="customerID"
                            placeholder="Enter your Customer ID" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" class="form-control form-control-lg" id="phone" name="phone"
                            placeholder="Enter your Phone Number" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg btn-block">Cancel Reservation</button>
                </form>

                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($message)) {
                    echo $message;
                }
                ?>
            </div>
        </div>

        <?php include 'footer.php'; ?>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>