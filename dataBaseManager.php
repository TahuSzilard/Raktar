<?php

class DatabaseManager {
    private $servername;
    private $username;
    private $password;
    private $dbname;
    private $conn;

    public function __construct($servername, $username, $password, $dbname) {
        $this->servername = $servername;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;
    }

    public function connect() {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getAllData() {
        $sql = "SELECT stores.name AS store_name, stores.address AS store_address,
        shelves.name AS shelf_name, `rows`.name AS row_name, columns.name AS column_name,
        products.name AS product_name, products.id AS id, quantity AS db, min_qty AS min_db
        FROM stores
        LEFT JOIN `rows` ON stores.id = `rows`.id_store
        LEFT JOIN columns ON `rows`.id = columns.id_row
        LEFT JOIN shelves ON columns.id = shelves.id_column
        LEFT JOIN products ON shelves.id = products.id_shelf";

        $result = $this->conn->query($sql);

        $data = array();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = array(
                    "product_id" => $row["id"],
                    "store_name" => $row["store_name"],
                    "store_address" => $row["store_address"],
                    "shelf_name" => $row["shelf_name"],
                    "row_name" => $row["row_name"],
                    "column_name" => $row["column_name"],
                    "product_name" => $row["product_name"],
                    "db" => $row["db"],
                    "min_db" => $row["min_db"]
                );
            }
        }

        return $data;
    }


    public function closeConnection() {
        $this->conn->close();
    }
    public function addData($storeName, $storeAddress, $shelfName, $rowName, $columnName, $productName, $minQty, $quantity, $price) {
        $stmt = $this->conn->prepare("INSERT INTO stores (name, address) VALUES (?, ?)");
        $stmt->bind_param("ss", $storeName, $storeAddress);
        $stmt->execute();
        $storeId = $this->conn->insert_id;

        $stmt = $this->conn->prepare("INSERT INTO `rows` (name, id_store) VALUES (?, ?)");
        $stmt->bind_param("si", $rowName, $storeId);
        $stmt->execute();
        $rowId = $this->conn->insert_id;

        $stmt = $this->conn->prepare("INSERT INTO columns (name, id_row) VALUES (?, ?)");
        $stmt->bind_param("si", $columnName, $rowId);
        $stmt->execute();
        $columnId = $this->conn->insert_id;

        $stmt = $this->conn->prepare("INSERT INTO shelves (name, id_column) VALUES (?, ?)");
        $stmt->bind_param("si", $shelfName, $columnId);
        $stmt->execute();
        $shelfId = $this->conn->insert_id;

        $stmt = $this->conn->prepare("INSERT INTO products (name, id_shelf, min_qty, quantity, price) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("siiid", $productName, $shelfId, $minQty, $quantity, $price);
        $stmt->execute();
    }
    public function getProductLocation($productName) {
        $sql = "SELECT shelves.name AS shelf_name, columns.name AS column_name, `rows`.name AS row_name, products.quantity
                FROM products
                LEFT JOIN shelves ON products.id_shelf = shelves.id
                LEFT JOIN columns ON shelves.id_column = columns.id
                LEFT JOIN `rows` ON columns.id_row = `rows`.id
                WHERE products.name = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $productName);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return array(
                "shelf_name" => $row["shelf_name"],
                "column_name" => $row["column_name"],
                "row_name" => $row["row_name"],
                "quantity" => $row["quantity"]
            );
        } else {
            return null;
        }
    }
 
}
