<?php
require_once 'header.php';
require_once "../../../vendor/autoload.php"; // Include PhpSpreadsheet or PHPExcel autoload file

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['excel_file'])) {
    $file = $_FILES['excel_file']['tmp_name'];

    try {
        // Load the spreadsheet file
        $spreadsheet = IOFactory::load($file);
        $worksheet = $spreadsheet->getActiveSheet();

        // Prepare SQL statements
        $insertSql = "INSERT INTO tbl_product (p_name, p_old_price, p_actual_price, p_gst, p_qty, p_featured_photo, p_is_featured, p_is_active, ecat_id)
                      VALUES (:p_name, :p_old_price, :p_actual_price, :p_gst, :p_qty, :p_featured_photo, :p_is_featured, :p_is_active, :ecat_id)";
        $checkSql = "SELECT COUNT(*) FROM tbl_product WHERE p_name = :p_name";

        $insertStmt = $pdo->prepare($insertSql);
        $checkStmt = $pdo->prepare($checkSql);

        // Iterate through each row in the worksheet
        $isFirstRow = true;
        foreach ($worksheet->getRowIterator() as $row) {
            // Skip the header row
            if ($isFirstRow) {
                $isFirstRow = false;
                continue;
            }

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $data = [];
            foreach ($cellIterator as $cell) {
                $data[] = $cell->getValue();
            }

            // Check if product already exists
            $checkStmt->execute([':p_name' => $data[0]]);
            $count = $checkStmt->fetchColumn();

            if ($count == 0) {
                // Bind values to the SQL statement and execute
                $insertStmt->execute([
                    ':p_name' => $data[0],
                    ':p_old_price' => $data[1],
                    ':p_actual_price' => $data[2],
                    ':p_gst' => $data[3],
                    ':p_qty' => $data[4],
                    ':p_featured_photo' => $data[5],
                    ':p_is_featured' => $data[6],
                    ':p_is_active' => $data[7],
                    ':ecat_id' => $data[8]
                ]);
            } else {
                // Handle existing product case
                $notAddedProducts[] = $data[0];
            }
        }

        // Redirect back to the products page with a success message
        $notAddedMessage = '';
        if (!empty($notAddedProducts)) {
            $notAddedMessage = 'The following products were not added because they already exist: ' . implode(', ', $notAddedProducts);
        }

        header('Location: product.php?import=success&message=' . urlencode($notAddedMessage));
        exit;
    } catch (Exception $e) {
        // Handle exceptions and show a generic error message
        header('Location: product.php?import=error&message=' . urlencode('An error occurred during the import process.'));
        exit;
    }
}
?>
