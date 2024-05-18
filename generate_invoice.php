
<?php
ob_start();
session_start();
include("admin/inc/config.php");
// Check if the customer is logged in or not
if(!isset($_SESSION['customer'])) {
    header('location: '.BASE_URL.'logout.php');
    exit;
} else {
    // If customer is logged in, but admin make him inactive, then force logout this user.
    $statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_id=? AND cust_status=?");
    $statement->execute(array($_SESSION['customer']['cust_id'],0));
    $total = $statement->rowCount();
    if($total) {
        header('location: '.BASE_URL.'logout.php');
        exit;
    }
}
?>
<?php
// Fetch data based on payment_id
$payment_id = $_POST['payment_id'];

// Query database to get payment details
$statement = $pdo->prepare("SELECT * FROM tbl_order WHERE payment_id=?");
$statement->execute(array($payment_id));
$orders = $statement->fetchAll(PDO::FETCH_ASSOC);

// Query database to get payment details
$statement = $pdo->prepare("SELECT * FROM tbl_payment WHERE payment_id=?");
$statement->execute(array($payment_id));
$payment_detail = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Invoice</title>
<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }
    th, td {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }
    th {
        background-color: #f2f2f2;
    }
</style>
</head>
<body>
    <h1>Invoice</h1>
    <table>
        <tr>
            <th>Product Name</th>
            <th>Size</th>
            <th>Color</th>
            <th>Quantity</th>
            <th>Unit Price</th>
        </tr>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td><?php echo $order['product_name']; ?></td>
            <td><?php echo $order['size']; ?></td>
            <td><?php echo $order['color']; ?></td>
            <td><?php echo $order['quantity']; ?></td>
            <td><?php echo $order['unit_price']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>