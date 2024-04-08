<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raktár</title>
    <link rel="stylesheet" type="text/css" href="index.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
    <div class="mainBody">
        <h1>Raktáraink</h1>
        <div class="upLoadBt">
            <form action="setupDatabase.php" method="post">
                <input type="submit" name="upload" value="Adatbázis feltöltése">
            </form>
            <form>
                <button><a href="pdf.php">PDF létrehozása</a></button>
            </form>
        </div>
        <br>
        <script src="index.js"></script>
        <div class="kiiratas">
            <div class="search-form">
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <h2>Keresés</h2>
                    <label for="storeName">Áruház:</label>
                    <input type="text" id="storeName" name="storeName" required>
                    <input type="submit" value="Keresés">
                </form>
            </div>
            <div class="search-form">
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <label for="productName">Termék:</label>
                    <input type="text" id="productName" name="productName" required>
                    <input type="submit" value="Keresés">
                </form>
            </div>
        </div>

        <div class="kiiratas newData">
            <button id="newDataBtn">Új termék hozzáadása</button>
            <div id="form" style="display: none;">
                <form id="newDataForm" action="add.php" method="post">
                    <label for="store_name">Áruház neve:</label><br>
                    <input type="text" id="store_name" name="store_name"><br>
                    <label for="store_address">Cím:</label><br>
                    <input type="text" id="store_address" name="store_address"><br>
                    <label for="shelf_name">Polc:</label><br>
                    <input type="text" id="shelf_name" name="shelf_name"><br>
                    <label for="row_name">Sor:</label><br>
                    <input type="text" id="row_name" name="row_name"><br>
                    <label for="column_name">Oszlop:</label><br>
                    <input type="text" id="column_name" name="column_name"><br>
                    <label for="product_name">Termék:</label><br>
                    <input type="text" id="product_name" name="product_name"><br>
                    <label for="min_qty">Minimális mennyiség:</label><br>
                    <input type="number" id="min_qty" name="min_qty" min="1"><br>
                    <label for="quantity">Mennyiség:</label><br>
                    <input type="number" id="quantity" name="quantity" min="1"><br>
                    <label for="price">Ár:</label><br>
                    <input type="number" id="price" name="price" step="0.01" min="0"><br><br>
                    <input type="button" id="addDataBtn" value="Hozzáadás">
                </form>
            </div>
        </div>

        <div class="kiiratas">
            <?php
            require_once 'dataBaseManager.php';

            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "shop";

            $databaseManager = new DatabaseManager($servername, $username, $password, $dbname);
            $databaseManager->connect();

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                
                if (isset($_POST["storeName"]) && isset($_POST["productName"]) && !empty($_POST["storeName"]) && !empty($_POST["productName"])) {

                    $searchedProduct = $_POST["productName"];

                    $productLocation = $databaseManager->getProductLocation($searchedProduct);

                    if ($productLocation !== null) {
                        echo "<p><strong>Termék:</strong> " . $searchedProduct . "</p>"; 
                        echo "<p><strong>Darabszám:</strong> " . $productLocation['quantity'] . "</p>"; 
                        echo "<p><strong>Polc:</strong> " . $productLocation['shelf_name'] . "</p>";
                        echo "<p><strong>Sor:</strong> " . $productLocation['row_name'] . "</p>";
                        echo "<p><strong>Oszlop:</strong> " . $productLocation['column_name'] . "</p>";
                        
                    } else {
                        
                        echo "<p>Nincs találat a keresett termékre.</p>";
                    }
                } else {
                    
               
                }
            }
            ?>
        </div>
        <div class="kiiratas">
            <?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shop";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<form method='post'>";
$sql = "SELECT DISTINCT name FROM stores";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<select name='storeName'>";
    echo "<option value='' disabled selected class='select-placeholder'>Válassz raktárat</option>"; 
    while($row = $result->fetch_assoc()) {
        echo "<option value='".$row["name"]."'>".$row["name"]."</option>";
    }
    echo "</select>";
} else {
    echo "0 results";
}
echo "<input type='submit' value='Keresés'>";
echo "</form>";

if(isset($_POST['storeName'])) {
    $storeName = $_POST['storeName'];
    
    $sql = "SELECT stores.name AS store_name, stores.address AS store_address,
    shelves.name AS shelf_name, `rows`.name AS row_name, columns.name AS column_name,
    products.name AS product_name, products.id AS id, products.quantity AS db, products.min_qty AS min_db, products.price AS ar,
    products.id AS product_id
    FROM stores
        LEFT JOIN `rows` ON stores.id = `rows`.id_store
        LEFT JOIN columns ON `rows`.id = columns.id_row
        LEFT JOIN shelves ON columns.id = shelves.id_column
        LEFT JOIN products ON shelves.id = products.id_shelf
        WHERE stores.name = '$storeName'";

    
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h2>Termékek a(z) $storeName boltban:</h2>";
        echo "<table border='1'>";
        echo "<tr><th>Termék neve</th><th>Cím</th><th>Polc</th><th>Oszlop</th><th>Sor</th><th>Bolt</th><th>Minimális mennyiség</th><th>Jelenlegi mennyiség</th><th>Ár</th><th>Törlés</th><th>Módosítás</th><th>Kevés termék</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>".$row["product_name"]."</td>";
            echo "<td>".$row["store_address"]."</td>";
            echo "<td>".$row["shelf_name"]."</td>";
            echo "<td>".$row["column_name"]."</td>";
            echo "<td>".$row["row_name"]."</td>";
            echo "<td>".$row["store_name"]."</td>";
            echo "<td>".$row["min_db"]."</td>";
            echo "<td>".$row["db"]."</td>";
            echo "<td>".$row["ar"]. " Ft"."</td>";
            echo "<td>
            <button class='delBtn' data-id='" . $row['product_id'] . "'>Adat törlése</button>
          </td>";
    
            echo "<td><p><form method='get' action='modify_product.php'>
                <input type='hidden' name='product_name' value='" . $row['product_name'] . "'>
                <input type='submit' value='Adat módosítása'>
                </form></p></td>";
            if ($row['min_db'] > $row['db']) {
                echo "<td><p style='color: red;'>Kevés van a termékből!</p></td>";
            }
            else{
                echo "<td><p style='color: green;'>Elegendő termék van!</p></td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Nincs találat a keresésre.";
    }
}

$conn->close();
?>
        </div>

    </div>
</body>
</html>
