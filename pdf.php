<?php
require('tfpdf.php');

// Adatbázis csatlakozás
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shop";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Sikertelen kapcsolódás: " . $conn->connect_error);
}

class PDF extends TFPDF
{
    // Load data from database
    function LoadDataFromDatabase($conn)
    {
        $data = array();

        // Lekérdezés az adatbázisból, belső összekapcsolás (inner join) használatával
        $query = "SELECT stores.name AS company, stores.address, products.min_qty, products.quantity, products.price
                  FROM stores
                  INNER JOIN `rows` ON stores.id = `rows`.id_store
                  INNER JOIN columns ON `rows`.id = columns.id_row
                  INNER JOIN shelves ON columns.id = shelves.id_column
                  INNER JOIN products ON shelves.id = products.id_shelf";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = array_values($row); // Hozzáadja az adatot a tömbhöz
            }
        }

        return $data;
    }

    function Header()
    {
        // Logo
        $this->Image('php.jpg',10,6,30);
        // Arial bold 15
        $this->SetFont('Arial','B',15);
        // Move to the right
        $this->Cell(80);
        // Title
        $this->Cell(30,10,'PHP PDF',1,0,'C');
        // Line break
        $this->Ln(20);
    }

    // Improved table
    function ImprovedTable($header, $data)
    {
        // Column widths
        $w = array(40, 50, 40, 40, 25);
        // Header
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C');
        $this->Ln();
        // Data
        foreach ($data as $row) {
            $this->Cell($w[0], 6, $row[0], 'LR', 0, 'C'); 
            $this->Cell($w[1], 6, $row[1], 'LR', 0, 'C'); 
            $this->Cell($w[2], 6, $row[2], 'LR', 0, 'C');
            $this->Cell($w[3], 6, $row[3], 'LR', 0, 'C');
            $this->Cell($w[4], 6, number_format($row[4], 2), 'LR', 0, 'C');
            $this->Ln();
        }
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }
}

$pdf = new PDF();
$data = $pdf->LoadDataFromDatabase($conn); // Adatok lekérése az adatbázisból
// Column headings

$header = array('Cég', 'Cím', 'Min. mennyiség', 'Jel. mennyiség', 'Ár');
$pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
$pdf->SetFont('Dejavu', '', 14);
$pdf->AddPage();
$pdf->ImprovedTable($header, $data);
$pdf->Output();
$conn->close(); 
?>
