<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raktár</title>
    <link rel="stylesheet" type="text/css" href="css/index.css"> 
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

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

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['product_name'])) {
    $product_name = $_GET['product_name'];
    $sql = "SELECT p.*, s.name as store_name, s.address as store_address, r.name as row_name, c.name as column_name, sh.name as shelf_name FROM products p
            LEFT JOIN shelves sh ON p.id_shelf = sh.id
            LEFT JOIN columns c ON sh.id_column = c.id
            LEFT JOIN `rows` r ON c.id_row = r.id
            LEFT JOIN stores s ON r.id_store = s.id
            WHERE p.name = '$product_name'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        ?>
        <div class="form-card">
        <form method="post" action="modify_product_logic.php">
            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
            Termék neve: <input type="text" name="name" value="<?php echo $row['name']; ?>"><br>
            Minimális mennyiség: <input type="text" name="min_qty" value="<?php echo $row['min_qty']; ?>"><br>
            Mennyiség: <input type="text" name="quantity" value="<?php echo $row['quantity']; ?>"><br>
            Ár: <input type="text" name="price" value="<?php echo $row['price']; ?>"><br>
            Áruház neve: <input type="text" name="store_name" value="<?php echo $row['store_name']; ?>"><br>
            Áruház címe: <input type="text" name="store_address" value="<?php echo $row['store_address']; ?>"><br>
            Sor: <input type="text" name="row_name" value="<?php echo $row['row_name']; ?>"><br>
            Oszlop: <input type="text" name="column_name" value="<?php echo $row['column_name']; ?>"><br>
            Polc: <input type="text" name="shelf_name" value="<?php echo $row['shelf_name']; ?>"><br>
            <input type="submit" value="Módosítás">
            <a href="index.php"><button type="button">Vissza</button></a>
        </form>
</div>
<?php
    } else {
        echo "Termék nem található!";
    }
} else {
    echo "A termék neve nincs megadva!";
}

$conn->close();

?>
