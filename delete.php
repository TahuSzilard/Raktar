<?php
if (isset($_POST['delete_id'])) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "shop";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $id = $_POST['delete_id'];

    $sqlDeleteProduct = "DELETE products, shelves, columns, `rows`, stores FROM products
                         LEFT JOIN shelves ON products.id_shelf = shelves.id
                         LEFT JOIN columns ON shelves.id_column = columns.id
                         LEFT JOIN `rows` ON columns.id_row = `rows`.id
                         LEFT JOIN stores ON `rows`.id_store = stores.id
                         WHERE products.id = $id";
    
    if ($conn->query($sqlDeleteProduct) === TRUE) {
        
        echo "Az adat és minden hozzá tartozó adat sikeresen törölve lett.";
    } else {
        
        echo "Hiba történt a törlés közben: " . $conn->error;
    }

    $conn->close();
}
?>
