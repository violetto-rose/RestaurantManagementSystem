<?php
session_start();

define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'pass@123');

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: admin_dashboard.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === ADMIN_USER && $password === ADMIN_PASS) {
        $_SESSION['loggedin'] = true;
        header('Location: admin_dashboard.php');
        exit();
    } else {
        $error = "Invalid username or password.";
    }
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
    <title>Admin Login - Swaad Sanchalan</title>
    <link rel="icon" href="IMG/favicon.ico" type="image/x-icon">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../CSS/admin_styles.css">
    <style>
        .login-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
        }

        .navbar {
            background-color: #492c07;
            padding: .5rem 1rem;
            transition: background-color 0.3s;
        }

        .navbar-brand img {
            max-height: 100px;
            width: auto;
        }

        .nav-link {
            transition: color 0.3s, transform 0.3s;
            color: white !important;
        }

        .nav-link:hover {
            color: #ff6347 !important;
            transform: scale(1.1);
        }

        .nav-item.active .nav-link {
            color: #ff6347 !important;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" href="#">
            <img src="../IMG/logo.svg" alt="Swaad Sanchalan Logo">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item <?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>">
                    <a class="nav-link" href="../index.php">Home</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="login-container">
        <h2 class="text-center">Admin Login</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>