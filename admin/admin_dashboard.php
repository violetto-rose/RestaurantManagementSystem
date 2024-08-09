<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'SwaadSanchalan';

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pagination settings
$resultsPerPage = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $resultsPerPage;

// Fetch total number of reservations
$totalReservationsResult = $conn->query("SELECT COUNT(*) as total FROM Reservation");
$totalReservations = $totalReservationsResult->fetch_assoc()['total'];
$totalPages = ceil($totalReservations / $resultsPerPage);

// Fetch reservations list with pagination and ordering
$reservationsResult = $conn->query("SELECT * FROM Reservation ORDER BY ReservationDate DESC, ReservationTime DESC LIMIT $offset, $resultsPerPage");

// Calculate total revenue today
$totalRevenueResult = $conn->query("SELECT SUM(Price) AS totalRevenue FROM Reservation WHERE DATE(ReservationDate) = CURDATE()");
$totalRevenueRow = $totalRevenueResult->fetch_assoc();
$totalRevenueToday = $totalRevenueRow['totalRevenue'] ?? 0;

// Calculate total customers
$totalCustomersResult = $conn->query("SELECT COUNT(*) AS totalCustomers FROM Customer");
$totalCustomersRow = $totalCustomersResult->fetch_assoc();
$totalCustomers = $totalCustomersRow['totalCustomers'] ?? 0;

// Calculate pending reservations (assuming pending means reservations for future dates)
$pendingReservationsResult = $conn->query("SELECT COUNT(*) AS pendingReservations FROM Reservation WHERE ReservationDate >= CURDATE()");
$pendingReservationsRow = $pendingReservationsResult->fetch_assoc();
$pendingReservations = $pendingReservationsRow['pendingReservations'] ?? 0;

// Fetch reservation counts by table type
$reservationCountsResult = $conn->query("SELECT TableType, COUNT(*) AS count FROM Reservation GROUP BY TableType");

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
    <title>Admin Dashboard</title>
    <link rel="icon" href="IMG/favicon.ico" type="image/x-icon">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap"
        rel="stylesheet">
    <link href="CSS/admin_styles.css" rel="stylesheet">
    <style>
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
    <script>
        // Function to save scroll position
        function saveScrollPosition() {
            sessionStorage.setItem('scrollToTable', 'true');
        }

        // Function to scroll to the reservations table
        function scrollToReservationsTable() {
            if (sessionStorage.getItem('scrollToTable') === 'true') {
                const reservationsTable = document.getElementById('reservationsTable');
                if (reservationsTable) {
                    reservationsTable.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    sessionStorage.removeItem('scrollToTable');
                }
            }
        }

        // Add event listeners
        document.addEventListener('DOMContentLoaded', scrollToReservationsTable);

        // Add click event listener to pagination links
        document.addEventListener('DOMContentLoaded', function () {
            let paginationLinks = document.querySelectorAll('.pagination .page-link');
            paginationLinks.forEach(function (link) {
                link.addEventListener('click', saveScrollPosition);
            });
        });
    </script>
</head>

<body>
    <?php include 'admin_header.php'; ?>

    <div class="container mt-5">
        <h2 class="text-center">Admin Dashboard</h2>

        <div class="row mt-4">
            <div class="col-md-3 d-flex align-items-stretch">
                <div class="card bg-info text-white flex-fill">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Total Revenue Today</h5>
                        <p class="card-text">₹<?php echo number_format($totalRevenueToday, 2); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 d-flex align-items-stretch">
                <div class="card bg-success text-white flex-fill">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Total Customers</h5>
                        <p class="card-text"><?php echo $totalCustomers; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 d-flex align-items-stretch">
                <div class="card bg-warning text-white flex-fill">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Pending Reservations</h5>
                        <p class="card-text"><?php echo $pendingReservations; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 d-flex align-items-stretch">
                <div class="card bg-primary text-white flex-fill">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Other Information</h5>
                        <ul class="list-unstyled">
                            <?php
                            if ($reservationCountsResult->num_rows > 0) {
                                while ($row = $reservationCountsResult->fetch_assoc()) {
                                    echo "<li><strong>{$row['TableType']}</strong>: {$row['count']} reservations</li>";
                                }
                            } else {
                                echo "<li>No reservations found</li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div id="reservationsTable" class="mt-4">
            <h3>Reservations List</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Reservation ID</th>
                        <th>Customer ID</th>
                        <th>Table ID</th>
                        <th>Reservation Date</th>
                        <th>Reservation Time</th>
                        <th>Number of Guests</th>
                        <th>Table Type</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($reservationsResult->num_rows > 0) {
                        while ($row = $reservationsResult->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['ReservationID']}</td>
                                <td>{$row['CustomerID']}</td>
                                <td>{$row['TableID']}</td>
                                <td>{$row['ReservationDate']}</td>
                                <td>{$row['ReservationTime']}</td>
                                <td>{$row['NumberOfGuests']}</td>
                                <td>{$row['TableType']}</td>
                                <td>₹{$row['Price']}</td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8' class='text-center'>No reservations found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <!-- Pagination -->
            <nav aria-label="Reservations pagination">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>

    <?php include 'admin_footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>