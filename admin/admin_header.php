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
    <link href="../CSS/admin_styles.css" rel="stylesheet">
</head>

<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="admin_dashboard.php">
            <img src="../IMG/logo.svg" alt="Swaad Sanchalan Logo">
        </a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item <?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>">
                    <a class="nav-link" href="../index.php">Home</a>
                </li>
                <li class="nav-item <?php echo ($currentPage == 'billing.php') ? 'active' : ''; ?>">
                    <a class="nav-link" href="billing.php">Billing</a>
                </li>
                <li class="nav-item <?php echo ($currentPage == 'sales_report.php') ? 'active' : ''; ?>">
                    <a class="nav-link" href="sales_report.php">Sales report</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>