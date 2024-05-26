<?php require_once('header.php'); ?>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
foreach ($result as $row) {
    $banner_checkout = $row['banner_checkout'];
}
?>

<?php
if(!isset($_SESSION['cart_p_id'])) {
    header('location: cart.php');
    exit;
}
?>
 
<?php 
  // Check if the form is submitted
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try{
        
        $cust_s_name = $_POST['cust_s_name'];
        $cust_s_cname = $_POST['cust_s_cname'];
        $cust_s_phone = $_POST['cust_s_phone'];
        $cust_s_country = $_POST['cust_s_country'];
        $cust_s_address = $_POST['cust_s_address'];
        $cust_s_city = $_POST['cust_s_city'];
        $cust_s_state = $_POST['cust_s_state'];
        $cust_s_zip = $_POST['cust_s_zip'];

        // Update the database
        $statement = $pdo->prepare("UPDATE tbl_customer SET 
            cust_s_name=?, cust_s_cname=?, cust_s_phone=?, 
            cust_s_country=?, cust_s_address=?, cust_s_city=?, 
            cust_s_state=?, cust_s_zip=? WHERE cust_id=?");
        $statement->execute(array(
            $cust_s_name, $cust_s_cname, $cust_s_phone, 
            $cust_s_country, $cust_s_address, $cust_s_city, 
            $cust_s_state, $cust_s_zip, $_SESSION['customer']['cust_id']
        ));

        // Update the session
        $_SESSION['customer']['cust_s_name'] = $cust_s_name;
        $_SESSION['customer']['cust_s_cname'] = $cust_s_cname;
        $_SESSION['customer']['cust_s_phone'] = $cust_s_phone;
        $_SESSION['customer']['cust_s_country'] = $cust_s_country;
        $_SESSION['customer']['cust_s_address'] = $cust_s_address;
        $_SESSION['customer']['cust_s_city'] = $cust_s_city;
        $_SESSION['customer']['cust_s_state'] = $cust_s_state;
        $_SESSION['customer']['cust_s_zip'] = $cust_s_zip;

        // Redirect or display success message
        header('Location: checkout.php');
        exit;
    } catch (Exception $e) {
        // Handle exceptions
        echo 'An error occurred: ' . $e->getMessage();
    }
  }
?>

<div class="page-banner" style="background-image: url(assets/uploads/<?php echo $banner_checkout; ?>)">
    <div class="overlay"></div>
    <div class="page-banner-inner">
        <h1><?php echo LANG_VALUE_22; ?></h1>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-6">
                <?php if(!isset($_SESSION['customer'])): ?>
                    <p>
                        <a href="login.php" class="btn btn-md btn-danger"><?php echo LANG_VALUE_160; ?></a>
                    </p>
                <?php else: ?>

                <h3 class="special"><?php echo LANG_VALUE_26; ?></h3>
                <div class="cart">
                    <table class="table table-responsive">
                        <tr>
                            <th><?php echo LANG_VALUE_7; ?></th>
                            <th><?php echo LANG_VALUE_8; ?></th>
                            <th><?php echo LANG_VALUE_47; ?></th>
                            <th><?php echo LANG_VALUE_157; ?></th>
                            <th><?php echo LANG_VALUE_158; ?></th>
                            <th><?php echo LANG_VALUE_159; ?></th>
                            <th><?php echo LANG_VALUE_55; ?></th>
                            <th class="text-right"><?php echo LANG_VALUE_82; ?></th>
                        </tr>
                        <?php
                        $table_total_price = 0;

                        $i=0;
                        foreach($_SESSION['cart_p_id'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_p_id[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_size_id'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_size_id[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_size_name'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_size_name[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_color_id'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_color_id[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_color_name'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_color_name[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_p_qty'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_p_qty[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_p_current_price'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_p_current_price[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_p_name'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_p_name[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_p_featured_photo'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_p_featured_photo[$i] = $value;
                        }
                        ?>
                        <?php for($i=1;$i<=count($arr_cart_p_id);$i++): ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td>
                                <img src="assets/uploads/<?php echo $arr_cart_p_featured_photo[$i]; ?>" alt="">
                            </td>
                            <td><?php echo $arr_cart_p_name[$i]; ?></td>
                            <td><?php echo $arr_cart_size_name[$i]; ?></td>
                            <td><?php echo $arr_cart_color_name[$i]; ?></td>
                            <td><?php echo LANG_VALUE_1; ?><?php echo $arr_cart_p_current_price[$i]; ?></td>
                            <td><?php echo $arr_cart_p_qty[$i]; ?></td>
                            <td class="text-right">
                                <?php
                                $row_total_price = $arr_cart_p_current_price[$i]*$arr_cart_p_qty[$i];
                                $table_total_price = $table_total_price + $row_total_price;
                                ?>
                                <?php echo LANG_VALUE_1; ?><?php echo $row_total_price; ?>
                            </td>
                        </tr>
                        <?php endfor; ?>           
                        <tr>
                            <th colspan="7" class="total-text"><?php echo LANG_VALUE_81; ?></th>
                            <th class="total-amount"><?php echo LANG_VALUE_1; ?><?php echo $table_total_price; ?></th>
                        </tr>
                        <?php
                        $statement = $pdo->prepare("SELECT * FROM tbl_shipping_cost WHERE country_id=?");
                        $statement->execute(array($_SESSION['customer']['cust_country']));
                        $total = $statement->rowCount();
                        if($total) {
                            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($result as $row) {
                                $shipping_cost = $row['amount'];
                            }
                        } else {
                            $statement = $pdo->prepare("SELECT * FROM tbl_shipping_cost_all WHERE sca_id=1");
                            $statement->execute();
                            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($result as $row) {
                                $shipping_cost = $row['amount'];
                            }
                        }                        
                        ?>
                        <tr>
                            <td colspan="7" class="total-text"><?php echo LANG_VALUE_84; ?></td>
                            <td class="total-amount"><?php echo LANG_VALUE_1; ?><?php echo $shipping_cost; ?></td>
                        </tr>
                        <tr>
                            <th colspan="7" class="total-text"><?php echo LANG_VALUE_82; ?></th>
                            <th class="total-amount">
                                <?php
                                $final_total = $table_total_price+$shipping_cost;
                                ?>
                                <?php echo LANG_VALUE_1; ?><?php echo $final_total; ?>
                            </th>
                        </tr>
                    </table> 
                </div>

              </div>
                
                    <div class="col-md-6">
                        <h3 class="special"><?php echo LANG_VALUE_162; ?></h3>
                        <table class="table table-responsive table-bordered bill-address">
                            <tr>
                                <td><?php echo LANG_VALUE_102; ?></td>
                                <td><?php echo $_SESSION['customer']['cust_s_name']; ?></p></td>
                            </tr>
                            <tr>
                                <td><?php echo LANG_VALUE_103; ?></td>
                                <td><?php echo $_SESSION['customer']['cust_s_cname']; ?></td>
                            </tr>
                            <tr>
                                <td><?php echo LANG_VALUE_104; ?></td>
                                <td><?php echo $_SESSION['customer']['cust_s_phone']; ?></td>
                            </tr>
                            <tr>
                                <td><?php echo LANG_VALUE_106; ?></td>
                                <td><?php
                                        $statement = $pdo->prepare("SELECT * FROM tbl_country WHERE country_id=?");
                                        $statement->execute(array($_SESSION['customer']['cust_s_country']));
                                        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($result as $row) {
                                            echo $row['country_name'];
                                        }
                                        ?>
                                </td>
                            </tr>
                            <tr>
                                <td><?php echo LANG_VALUE_107; ?></td>
                                <td><?php echo $_SESSION['customer']['cust_s_address']; ?></td>
                            </tr>
                            <tr>
                                <td><?php echo LANG_VALUE_108; ?></td>
                                <td><?php echo $_SESSION['customer']['cust_s_city']; ?></td>
                            </tr>
                            <tr>
                                <td><?php echo LANG_VALUE_109; ?></td>
                                <td><?php echo $_SESSION['customer']['cust_s_state']; ?></td>
                            </tr>
                            <tr>
                                <td><?php echo LANG_VALUE_110; ?></td>
                                <td><?php echo $_SESSION['customer']['cust_s_zip']; ?></td>
                            </tr>
                        </table>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addressModal">
                            Edit Address
                        </button>
                    </div>
                </div>

            <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Address Modal -->
<div class="modal fade" id="addressModal" tabindex="-1" role="dialog" aria-labelledby="addressModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="addressModalLabel">Shipping Address</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addressForm" method="post" action="">
                    <div class="form-group">
                        <label for="cust_s_name">Name</label>
                        <input type="text" class="form-control" id="cust_s_name" name="cust_s_name" value="<?php echo $_SESSION['customer']['cust_s_name']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="cust_s_cname">Company Name</label>
                        <input type="text" class="form-control" id="cust_s_cname" name="cust_s_cname" value="<?php echo $_SESSION['customer']['cust_s_cname']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="cust_s_phone">Phone</label>
                        <input type="text" class="form-control" id="cust_s_phone" name="cust_s_phone" value="<?php echo $_SESSION['customer']['cust_s_phone']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="cust_s_country">Country</label>
                        <select class="form-control" id="cust_s_country" name="cust_s_country">
                        <?php
                            $statement = $pdo->prepare("SELECT * FROM tbl_country ORDER BY country_name ASC");
                            $statement->execute();
                            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($result as $row) {
                                            ?>
                                            <option value="<?php echo $row['country_id']; ?>" <?php if($row['country_id'] == $_SESSION['customer']['cust_s_country']) {echo 'selected';} ?>><?php echo $row['country_name']; ?></option>
                                            <?php
                                        }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="cust_s_address">Address</label>
                        <textarea class="form-control" id="cust_s_address" name="cust_s_address"><?php echo $_SESSION['customer']['cust_s_address']; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="cust_s_city">City</label>
                        <input type="text" class="form-control" id="cust_s_city" name="cust_s_city" value="<?php echo $_SESSION['customer']['cust_s_city']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="cust_s_state">State</label>
                        <input type="text" class="form-control" id="cust_s_state" name="cust_s_state" value="<?php echo $_SESSION['customer']['cust_s_state']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="cust_s_zip">Zip Code</label>
                        <input type="text" class="form-control" id="cust_s_zip" name="cust_s_zip" value="<?php echo $_SESSION['customer']['cust_s_zip']; ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>

<!-- Include Bootstrap JS and dependencies (ensure they are included only once in your project) -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
