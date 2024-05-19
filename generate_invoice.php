<?php
ob_start();
session_start();
include("admin/inc/config.php");

// Check if the customer is logged in or not
if (!isset($_SESSION['customer'])) {
    header('location: ' . BASE_URL . 'logout.php');
    exit;
} else {
    // If customer is logged in, but admin make him inactive, then force logout this user.
    $statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_id=? AND cust_status=?");
    $statement->execute(array($_SESSION['customer']['cust_id'], 0));
    $total = $statement->rowCount();
    if ($total) {
        header('location: ' . BASE_URL . 'logout.php');
        exit;
    }
}

// Fetch data based on payment_id
$payment_id = $_POST['payment_id'];

// Query database to get payment details
$statement = $pdo->prepare("SELECT * FROM tbl_order WHERE payment_id=?");
$statement->execute(array($payment_id));
$orders = $statement->fetchAll(PDO::FETCH_ASSOC);

// Initialize TCPDF library
require_once('C:\xampp\phpMyAdmin\vendor\tecnickcom\tcpdf\tcpdf.php');
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Invoice');
$pdf->SetSubject('Invoice');

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 12);

// Add a title
$pdf->SetFont('helvetica', 'B', 16); // Bold, larger font size
$pdf->Cell(0, 10, 'Invoice', 0, 1, 'C', 0, '', 0, false, 'T', 'M'); // Set y position slightly lower
$pdf->SetFont('helvetica', '', 12); // Restore default font size

// Add customer details
$pdf->Cell(0, 10, 'Customer Details:', 0, true, 'L', 0, '', 0, false, 'T', 'M');
$pdf->Cell(0, 10, 'Name: ' . $_SESSION['customer']['cust_name'], 0, true, 'L', 0, '', 0, false, 'T', 'M');
$pdf->Cell(0, 10, 'Email: ' . $_SESSION['customer']['cust_email'], 0, true, 'L', 0, '', 0, false, 'T', 'M');
// Add more customer details if needed

// Add a title for the itemized list
$pdf->Cell(0, 10, 'Purchased Items:', 0, true, 'L', 0, '', 0, false, 'T', 'M');

// Calculate total width of the table
$totalWidth = $pdf->GetPageWidth() - $pdf->GetX() + 35; // Subtract current X position to get remaining width on the page

// Table Header
$pdf->SetFont('helvetica', 'B', 10);
$header = array('Product Name', 'Size', 'Color', 'Quantity', 'Unit Price');
$pdf->SetFillColor(180, 180, 180);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(0);
$pdf->SetLineWidth(0.5);

// Calculate dynamic column width for Product Name
$maxProductNameLength = max(array_map('strlen', array_column($orders, 'product_name')));
$dynamicProductNameWidth = ($totalWidth - 20 - 20 - 20 - 30) * 0.65; // Adjust multiplier as needed
$pdf->Cell($dynamicProductNameWidth, 10, $header[0], 1, 0, 'C', 1);
$pdf->Cell(20, 10, $header[1], 1, 0, 'C', 1);
$pdf->Cell(20, 10, $header[2], 1, 0, 'C', 1);
$pdf->Cell(20, 10, $header[3], 1, 0, 'C', 1);
$pdf->Cell(30, 10, $header[4], 1, 1, 'C', 1);

// Table Data
$pdf->SetFont('helvetica', '', 10);
foreach ($orders as $order) {
    // Use Cell for all cells to maintain alignment in the same row
    $pdf->Cell($dynamicProductNameWidth, 10, $order['product_name'], 1, 0, 'L');
    $pdf->Cell(20, 10, $order['size'], 1, 0, 'C');
    $pdf->Cell(20, 10, $order['color'], 1, 0, 'C');
    $pdf->Cell(20, 10, $order['quantity'], 1, 0, 'C');
    $pdf->Cell(30, 10, $order['unit_price'], 1, 1, 'C');
}

// Total Cost
$total_amount = array_sum(array_column($orders, 'unit_price'));
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell($dynamicProductNameWidth + 20 + 20 + 20, 10, 'Total Cost', 1, 0, 'L');
$pdf->Cell(30, 10, '$' . $total_amount, 1, 1, 'C');
$pdf->Ln(); // Move to next line

// Set the file name
$filename = "invoice_" . $payment_id . ".pdf";

// Output PDF as a file
$pdf->Output($filename, 'D');

// Cleanup
$pdf->Close();
ob_end_flush();
exit;
?>
