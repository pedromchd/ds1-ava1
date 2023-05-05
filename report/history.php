<?php
session_start();

$user = $_SESSION['user'];
$name = $_SESSION['name'];

if ($user !== 1 || $name !== 'admin') {
  header('Location: /home.php');
  exit;
}

$db = new mysqli('localhost', 'root', '', 'library');
$result = $db->query('SELECT * FROM deleted ORDER BY exclusion DESC');
$db->close();

require_once('../tcpdf/tcpdf.php');

class MYPDF extends TCPDF {
  function MultiRow(array $row = []) {
    $cellWidth = ($this->getPageWidth() - 20) / sizeof($row);
    $y_start = $y_end = $this->GetY();
    foreach ($row as $value) {
      $this->MultiCell($cellWidth, 0, $value, 1, 'L', true, 2, $this->GetX(), $y_start, true, 0);
      $y_end = max($this->GetY(), $y_end);
    }
    $this->setXY(10, $y_end);
  }
}

$pdf = new MYPDF('L', 'mm', 'A4', true, 'UTF-8');

$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor('admin');
$pdf->setTitle('Remove History');

$pdf->setHeaderData('', 0, 'Game Remove History', 'by Querty Library', array(0, 0, 0), array(0, 0, 0));
$pdf->setHeaderFont(array('helvetica', 'B', 16));
$pdf->setHeaderMargin(10);

$pdf->setFooterData(array(0, 0, 0), array(0, 0, 0));
$pdf->setFooterFont(array('helvetica', 'R', 10));
$pdf->setFooterMargin(10);

$pdf->setMargins(10, 30, 10);
$pdf->setAutoPageBreak(true, 20);

$pdf->AddPage();

$pdf->setCellPaddings(2, 2, 2, 2);
$pdf->setLineStyle(array('width' => 0.25, 'color' => array(74, 222, 128)));

$pdf->setFillColorArray(array(134, 239, 172));
$pdf->setFont('helvetica', 'B', 12);

$pdf->MultiRow(array('ID', 'Name', 'Year', 'System', 'Developer', 'User', 'Exclusion'));

$pdf->setFillColorArray(array(187, 247, 208));
$pdf->setFont('helvetica', 'R', 12);

while ($row = $result->fetch_assoc()) {
  $pdf->MultiRow($row);
}

$pdf->Output('history.pdf', 'I');
exit;
