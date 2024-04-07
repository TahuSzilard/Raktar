<?php

$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "shop"; 

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Sikertelen kapcsolódás: " . $conn->connect_error);
}

$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Az adatbázis létrehozva vagy már létezett.";
} else {
    echo "Hiba az adatbázis létrehozásakor: " . $conn->error;
}

$conn->select_db($dbname);

$sql = "CREATE TABLE IF NOT EXISTS stores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL
)";

$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS rowss (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
)";

$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS columns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
)";

$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS shelves (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
)";

$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_store INT,
    id_row INT,
    id_column INT,
    id_shelf INT,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL,
    min_quantity INT NOT NULL,
    FOREIGN KEY (id_store) REFERENCES stores(id),
    FOREIGN KEY (id_row) REFERENCES rowss(id),
    FOREIGN KEY (id_column) REFERENCES columns(id),
    FOREIGN KEY (id_shelf) REFERENCES shelves(id)
)";

$conn->query($sql);

echo "Az adatbázis és táblák létrehozva.";

$conn->close();

?>