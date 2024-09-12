<?php
session_start();
require '../config/config.php';
require '../config/common.php';
if($_SESSION['role'] !=1){
    echo "<script>alert('You are not admin');window.location.href='login.php';</script>";
}
// Initialize error variables
$rowError = $priceError = $existError = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['row_number']) || empty($_POST['price'])) {
        if (empty($_POST['row_number'])) {
            $rowError = "Row number is required";
        }
        if (empty($_POST['price'])) {
            $priceError = "Price is required";
        }
    } else {
        $row_number = $_POST['row_number'];
        $price = $_POST['price'];

        if (!is_numeric($price) || $price < 0) {
            $priceError = "Please enter a valid price";
        } else {
            // Check if the row number already exists
            $checkSql = "SELECT COUNT(*) FROM rowandprice WHERE row_number = :row_number";
            $checkStmt = $pdo->prepare($checkSql);
            $checkStmt->execute([':row_number' => $row_number]);
            $count = $checkStmt->fetchColumn();

            if ($count > 0) {
                $existError = "Row number already exists";
            } else {
                // Insert the new row price into the database
                $sql = "INSERT INTO rowandprice (row_number, price) VALUES (:row_number, :price)";
                $stmt = $pdo->prepare($sql);
                $result = $stmt->execute([
                    ':row_number' => $row_number,
                    ':price' => $price
                ]);

                if ($result) {
                    echo "<script>alert('Row price added');window.location.href = 'rowandprice_manage.php';</script>";
                } else {
                    echo "<script>alert('Failed to add row price');</script>";
                }
            }
        }
    }
}

include('header.php');
?>

<!-- Main content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="rowandprice_add.php" method="post">
                            <input name="_token" type="hidden" value="<?php echo $_SESSION['_token']; ?>">
                            <div class="form-group">
                                <label for="row_number">Row Number</label>
                                <p style="color:red"><?php echo empty($rowError) ? '' : '*' . $rowError; ?></p>
                                <input type="number" class="form-control" name="row_number" value="">
                            </div>
                            <div class="form-group">
                                <label for="price">Price</label>
                                <p style="color:red"><?php echo empty($priceError) ? '' : '*' . $priceError; ?></p>
                                <input type="number" class="form-control" name="price" step="0.01" value="">
                            </div>
                            <?php if (!empty($existError)) { ?>
                                <p class="text-danger"><?php echo $existError; ?></p>
                            <?php } ?>
                            <div class="form-group">
                                <input type="submit" class="btn btn-success" value="SUBMIT">
                                <a href="rowandprice_manage.php" class="btn btn-warning">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content -->
<?php include('footer.html') ?>
