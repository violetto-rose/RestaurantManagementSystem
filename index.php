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
    <title>Swaad Sanchalan - Restaurant Management System</title>
    <link rel="icon" href="IMG/favicon.ico" type="image/x-icon">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap"
        rel="stylesheet">
    <link href="CSS/styles.css" rel="stylesheet">
</head>

<body>
    <?php $currentPage = 'index.php';
    include 'header.php'; ?>

    <!-- Main Content -->
    <div class="row">
        <div class="col-lg-12 text-center">
            <img src="IMG/welcome.svg" alt="Restaurant Image" class="img-fluid">
        </div>
    </div>
    <div class="container mt-5">

        <div class="row mt-5 justify-content-center">
            <div class="col-md-5">
                <div class="card custom-card">
                    <img src="IMG/1.svg" class="card-img-top" alt="Menu">
                    <div class="card-body">
                        <h5 class="card-title">Our Menu</h5>
                        <p class="card-text">Discover our delicious dishes.</p>
                        <a href="menu.php" class="btn btn-primary">View Menu</a>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card custom-card">
                    <img src="IMG/2.svg" class="card-img-top" alt="Reservation">
                    <div class="card-body">
                        <h5 class="card-title">Make a Reservation</h5>
                        <p class="card-text">Book a table in advance.</p>
                        <a href="reservation.php" class="btn btn-primary">Reserve Now</a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var img = document.querySelector('.img-fluid');
            img.onload = function () {
                img.style.display = 'block';
            };
            img.src = img.src;
        });
    </script>
</body>

</html>