<!-- Footer -->
<footer class="custom-footer text-center text-lg-start mt-5">
    <?php
    // Get the current page filename
    $current_page = basename($_SERVER['PHP_SELF']);

    // Check if the current page is menu.php
    if ($current_page === 'menu.php') {
        echo '<style>
            .custom-footer {
                margin-top: 0 !important;
            }
        </style>';
    }
    ?>
    <div class="container p-4">
        <div class="row">
            <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                <h5 class="text-uppercase">Swaad Sanchalan</h5>
                <p>Effortless Management Exceptional Dining</p>
            </div>
            <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                <h5 class="text-uppercase">Contact</h5>
                <ul class="list-unstyled mb-0">
                    <li>
                        <a href="#!" class="custom-link">Email: info@swaadsanchalan.com</a>
                    </li>
                    <li>
                        <a href="#!" class="custom-link">Phone: +123 456 7890</a>
                    </li>
                    <li>
                        <a href="#!" class="custom-link">Address: 123 Restaurant St, Food City</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
        &copy; 2024 Swaad Sanchalan
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>