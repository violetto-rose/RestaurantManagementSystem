<?php
if (isset($_GET['tableType'])) {
    $tableType = $_GET['tableType'];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "swaadsanchalan";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT MAX(Capacity) AS maxCapacity FROM RestaurantTable WHERE TableType = ?");
    $stmt->bind_param("s", $tableType);
    $stmt->execute();
    $stmt->bind_result($maxCapacity);
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    echo json_encode(['maxCapacity' => $maxCapacity]);
} else {
    echo json_encode(['maxCapacity' => 0]);
}
