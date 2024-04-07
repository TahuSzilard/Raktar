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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $min_qty = $_POST['min_qty'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $store_name = $_POST['store_name'];
    $store_address = $_POST['store_address'];
    $row_name = $_POST['row_name'];
    $column_name = $_POST['column_name'];
    $shelf_name = $_POST['shelf_name'];

    
    $sql = "UPDATE stores SET name='$store_name', address='$store_address' WHERE name IN (
            SELECT s.name FROM products p
            LEFT JOIN shelves sh ON p.id_shelf = sh.id
            LEFT JOIN columns c ON sh.id_column = c.id
            LEFT JOIN `rows` r ON c.id_row = r.id
            LEFT JOIN stores s ON r.id_store = s.id
            WHERE p.id='$product_id')";
    $conn->query($sql);

    
    $sql = "UPDATE `rows` SET name='$row_name' WHERE name IN (
            SELECT r.name FROM products p
            LEFT JOIN shelves sh ON p.id_shelf = sh.id
            LEFT JOIN columns c ON sh.id_column = c.id
            LEFT JOIN `rows` r ON c.id_row = r.id
            LEFT JOIN stores s ON r.id_store = s.id
            WHERE p.id='$product_id')";
    $conn->query($sql);

    
    $sql = "UPDATE columns SET name='$column_name' WHERE name IN (
            SELECT c.name FROM products p
            LEFT JOIN shelves sh ON p.id_shelf = sh.id
            LEFT JOIN columns c ON sh.id_column = c.id
            LEFT JOIN `rows` r ON c.id_row = r.id
            LEFT JOIN stores s ON r.id_store = s.id
            WHERE p.id='$product_id')";
    $conn->query($sql);

    
    $sql = "UPDATE shelves SET name='$shelf_name' WHERE name IN (
            SELECT sh.name FROM products p
            LEFT JOIN shelves sh ON p.id_shelf = sh.id
            LEFT JOIN columns c ON sh.id_column = c.id
            LEFT JOIN `rows` r ON c.id_row = r.id
            LEFT JOIN stores s ON r.id_store = s.id
            WHERE p.id='$product_id')";
    $conn->query($sql);

    
    $sql = "UPDATE products SET name='$name', min_qty='$min_qty', quantity='$quantity', price='$price' WHERE id='$product_id'";
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$conn->close();

?>
