<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shop";

$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbname'");
if ($result->num_rows == 0) {

    $createDBQuery = "CREATE DATABASE $dbname";
    if ($conn->query($createDBQuery) === TRUE) {
        echo "Database created successfully\n";
    } else {
        die("Error creating database: " . $conn->error);
    }
}

$conn->select_db($dbname);

$sqlCreateStoresTable = "CREATE TABLE IF NOT EXISTS stores (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL
)";

$sqlCreateRowsTable = "CREATE TABLE IF NOT EXISTS `rows` (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    id_store INT(6) UNSIGNED,
    FOREIGN KEY (id_store) REFERENCES stores(id)
)";

$sqlCreateColumnsTable = "CREATE TABLE IF NOT EXISTS columns (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    id_row INT(6) UNSIGNED,
    FOREIGN KEY (id_row) REFERENCES `rows`(id)
)";

$sqlCreateShelvesTable = "CREATE TABLE IF NOT EXISTS shelves (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    id_column INT(6) UNSIGNED,
    FOREIGN KEY (id_column) REFERENCES columns(id)
)";

$sqlCreateProductsTable = "CREATE TABLE IF NOT EXISTS products (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    id_shelf INT(6) UNSIGNED,
    min_qty INT(6),
    quantity INT(6),
    price DECIMAL(10, 2),
    FOREIGN KEY (id_shelf) REFERENCES shelves(id)
)";

$conn->query($sqlCreateStoresTable);
$conn->query($sqlCreateRowsTable);
$conn->query($sqlCreateColumnsTable);
$conn->query($sqlCreateShelvesTable);
$conn->query($sqlCreateProductsTable);

$csvFile = 'adatok.txt';

if (($handle = fopen($csvFile, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        $storeName = $data[0];
        $storeAddress = $data[1];
        $rowName = $data[2];
        $columnName = $data[3];
        $shelfName = $data[4];
        $productName = $data[5];
        $minQty = $data[6];
        $quantity = $data[7];
        $price = $data[8];

        
        $sql = "INSERT INTO stores (name, address) VALUES ('$storeName', '$storeAddress')";
        $conn->query($sql);
        $storeId = $conn->insert_id;

        $sql = "INSERT INTO `rows` (name, id_store) VALUES ('$rowName', '$storeId')";
        $conn->query($sql);
        $rowId = $conn->insert_id;

        $sql = "INSERT INTO columns (name, id_row) VALUES ('$columnName', '$rowId')";
        $conn->query($sql);
        $columnId = $conn->insert_id;

        $sql = "INSERT INTO shelves (name, id_column) VALUES ('$shelfName', '$columnId')";
        $conn->query($sql);
        $shelfId = $conn->insert_id;

        $sql = "INSERT INTO products (name, id_shelf, min_qty, quantity, price) VALUES ('$productName', '$shelfId', '$minQty', '$quantity', '$price')";
        $conn->query($sql);
    }
    fclose($handle);
}

$conn->close();

?>
