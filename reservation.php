<?php
$reservationMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "SwaadSanchalan";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $conn->begin_transaction();

    try {
        $customerName = $_POST['customerName'] ?? '';
        $customerPhone = $_POST['customerPhone'] ?? '';
        $customerEmail = $_POST['customerEmail'] ?? '';
        $reservationDate = $_POST['reservationDate'] ?? '';
        $reservationTime = $_POST['reservationTime'] ?? '';
        $numberOfGuests = $_POST['numberOfGuests'] ?? '';
        $tableType = $_POST['tableType'] ?? '';

        if (empty($tableType)) {
            throw new Exception("Table type is not selected.");
        }

        $tablePrices = [
            'normal' => 100,
            'premium' => 200,
            'vip' => 500
        ];
        $price = $tablePrices[$tableType] ?? 0;

        do {
            $customerID = rand(1, 9999);
            $stmt = $conn->prepare("SELECT COUNT(*) FROM Customer WHERE CustomerID = ?");
            $stmt->bind_param("i", $customerID);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();
        } while ($count > 0);

        $stmt = $conn->prepare("INSERT INTO Customer (CustomerID, Name, Phone, Email) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $customerID, $customerName, $customerPhone, $customerEmail);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("SELECT TableID FROM RestaurantTable WHERE TableType = ? AND IsAvailable = TRUE AND Capacity >= ? LIMIT 1");
        $stmt->bind_param("si", $tableType, $numberOfGuests);
        $stmt->execute();
        $stmt->bind_result($tableID);
        $stmt->fetch();
        $stmt->close();

        if ($tableID) {
            // Mark the table as unavailable
            $stmt = $conn->prepare("UPDATE RestaurantTable SET IsAvailable = FALSE WHERE TableID = ?");
            $stmt->bind_param("i", $tableID);
            $stmt->execute();
            $stmt->close();

            // Insert the reservation
            $stmt = $conn->prepare("INSERT INTO Reservation (CustomerID, TableID, ReservationDate, ReservationTime, NumberOfGuests, TableType, Price) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iississ", $customerID, $tableID, $reservationDate, $reservationTime, $numberOfGuests, $tableType, $price);

            if ($stmt->execute()) {
                $conn->commit();
                $reservationMessage = "<div class='alert alert-success text-center'>
                    Reservation successful! Your Customer ID is: <strong>" . $customerID . "</strong>. 
                    <br><br>Please remember this ID for future reference, especially for checking or canceling your reservation.
                </div>";
            } else {
                throw new Exception("Failed to insert reservation: " . $stmt->error);
            }
        } else {
            throw new Exception("No available table found for the selected type and capacity.");
        }

        $stmt->close();
    } catch (Exception $e) {
        $conn->rollback();
        $reservationMessage = "<div class='alert alert-danger text-center'>Error: " . $e->getMessage() . "</div>";
    }

    $conn->close();
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
    <title>Swaad Sanchalan Reservation</title>
    <link rel="icon" href="IMG/favicon.ico" type="image/x-icon">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="CSS/styles.css" rel="stylesheet">
    <script>
        async function fetchAvailableTables(tableType) {
            if (tableType) {
                try {
                    const response = await fetch(`get_capacity.php?tableType=${tableType}`);
                    const data = await response.json();
                    const capacityInput = document.getElementById('numberOfGuests');
                    capacityInput.setAttribute('max', data.maxCapacity);
                    if (capacityInput.value > data.maxCapacity) {
                        capacityInput.value = data.maxCapacity;
                    }
                } catch (error) {
                    console.error('Error fetching table capacity:', error);
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const reservationDateInput = document.getElementById('reservationDate');
            const reservationTimeInput = document.getElementById('reservationTime');
            const tableTypeSelect = document.getElementById('tableType');
            const numberOfGuestsInput = document.getElementById('numberOfGuests');

            // Set min and max date for reservation
            const today = new Date();
            const maxDate = new Date(today);
            maxDate.setDate(maxDate.getDate() + 10);

            reservationDateInput.min = today.toISOString().split('T')[0];
            reservationDateInput.max = maxDate.toISOString().split('T')[0];

            function updateTimeOptions() {
                reservationTimeInput.innerHTML = '';
                const startTime = new Date(reservationDateInput.value + 'T09:00');
                const endTime = new Date(reservationDateInput.value + 'T22:00');

                while (startTime <= endTime) {
                    const option = document.createElement('option');
                    option.value = startTime.toTimeString().slice(0, 5);
                    option.textContent = startTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    reservationTimeInput.appendChild(option);
                    startTime.setMinutes(startTime.getMinutes() + 30);
                }

                // If it's today, disable past times
                if (reservationDateInput.value === today.toISOString().split('T')[0]) {
                    const currentTime = new Date();
                    currentTime.setHours(currentTime.getHours() + 1);
                    Array.from(reservationTimeInput.options).forEach(option => {
                        const optionTime = new Date(reservationDateInput.value + 'T' + option.value);
                        option.disabled = optionTime <= currentTime;
                    });
                }
            }

            reservationDateInput.addEventListener('change', updateTimeOptions);
            updateTimeOptions();

            tableTypeSelect.addEventListener('change', function () {
                const tableType = this.value;
                if (tableType) {
                    numberOfGuestsInput.disabled = false;
                    fetchAvailableTables(tableType);
                    numberOfGuestsInput.value = 1;
                } else {
                    numberOfGuestsInput.disabled = true;
                    numberOfGuestsInput.value = '';
                }
            });

            numberOfGuestsInput.disabled = true;
        });
    </script>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5">
        <?php
        if (!empty($reservationMessage)) {
            echo $reservationMessage;
        }
        ?>

        <h2 class="text-center">Make a Reservation</h2>
        <form id="reservationForm" method="post" action="">
            <div class="form-group">
                <label for="customerName">Name</label>
                <input type="text" class="form-control form-control-lg" id="customerName" name="customerName"
                    placeholder="Enter your name" required>
            </div>
            <div class="form-group">
                <label for="customerPhone">Phone</label>
                <input type="text" class="form-control form-control-lg" id="customerPhone" name="customerPhone"
                    placeholder="Enter your phone number" required>
            </div>
            <div class="form-group">
                <label for="customerEmail">Email</label>
                <input type="email" class="form-control form-control-lg" id="customerEmail" name="customerEmail"
                    placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="reservationDate">Date</label>
                <input type="date" class="form-control form-control-lg" id="reservationDate" name="reservationDate"
                    required>
            </div>
            <div class="form-group">
                <label for="reservationTime">Time</label>
                <select class="form-control form-control-lg" id="reservationTime" name="reservationTime" required>
                    <!-- Time options will be populated by JavaScript -->
                </select>
            </div>
            <div class="form-group">
                <label for="tableType">Table Type</label>
                <select id="tableType" name="tableType" class="form-control form-control-lg" required>
                    <option value="">Select Table Type</option>
                    <option value="normal">Normal - ₹100</option>
                    <option value="premium">Premium - ₹200</option>
                    <option value="vip">VIP - ₹500</option>
                </select>
            </div>
            <div class="form-group">
                <label for="numberOfGuests">Number of Guests</label>
                <input type="number" class="form-control form-control-lg" id="numberOfGuests" name="numberOfGuests"
                    placeholder="Enter number of guests" min="1" disabled required>
            </div>
            <button type="submit" class="btn btn-primary btn-lg btn-block">Submit</button>
        </form>

        <div class="mt-4 text-center">
            <a href="check_reservation.php" class="btn btn-secondary">Check Your Reservation</a>
        </div>

        <!-- Notices Section -->
        <div class="mt-5">
            <h4 class="text-center">Important Notices</h4>
            <ul class="list-group">
                <li class="list-group-item">If not checked in at reservation time, the reservation will be canceled
                    after 15 minutes.</li>
                <li class="list-group-item">The reservation time will be allocated for a 1-hour period.</li>
                <li class="list-group-item">The reservation cost will be added to your final bill.</li>
                <li class="list-group-item">Please ensure to arrive on time to avoid any inconvenience.</li>
            </ul>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>