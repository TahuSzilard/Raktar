<?php

require_once 'databaseManager.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shop";

$databaseManager = new DatabaseManager($servername, $username, $password, $dbname);
$databaseManager->connect();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['store_name']) && !empty($_POST['store_address']) && !empty($_POST['shelf_name']) && !empty($_POST['row_name']) && !empty($_POST['column_name']) && !empty($_POST['product_name']) && isset($_POST['min_qty']) && isset($_POST['quantity']) && isset($_POST['price'])) {
        $storeName = $_POST['store_name'];
        $storeAddress = $_POST['store_address'];
        $shelfName = $_POST['shelf_name'];
        $rowName = $_POST['row_name'];
        $columnName = $_POST['column_name'];
        $productName = $_POST['product_name'];
        $minQty = $_POST['min_qty'];
        $quantity = $_POST['quantity'];
        $price = $_POST['price'];

        $databaseManager->addData($storeName, $storeAddress, $shelfName, $rowName, $columnName, $productName, $minQty, $quantity, $price);

        header("Location: index.php");
        exit();
    } else {
        echo "Hiba: Nem sikerült az adatok hozzáadása, minden mezőt ki kell tölteni.";
    }
}

$databaseManager->closeConnection();

?>
