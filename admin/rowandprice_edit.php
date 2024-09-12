<?php
session_start();
require '../config/config.php';
require '../config/common.php';

// Check if the user is logged in
if (empty($_SESSION['user_id']) || empty($_SESSION['login_time'])) {
    header('Location: login.php');
    exit();
}
if($_SESSION['role'] !=1){
    echo "<script>alert('You are not admin');window.location.href='login.php';</script>";
}
// Get the ID from the URL parameter
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Redirect if ID is invalid
if ($id <= 0) {
    header('Location: rowandprice_list.php');
    exit();
}

// Fetch the current row and price data
$sql = "SELECT * FROM rowandprice WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// Redirect if no record found
if (!$row) {
    header('Location: rowandprice_list.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $row_number = $_POST['row_number'];
    $price = $_POST['price'];
    
    // Basic validation
    if (empty($row_number) || empty($price)) {
        $error = "All fields are required.";
    } else {
        // Check if the row number already exists (excluding the current record)
        $checkSql = "SELECT COUNT(*) FROM rowandprice WHERE row_number = :row_number AND id != :id";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->bindValue(':row_number', $row_number);
        $checkStmt->bindValue(':id', $id, PDO::PARAM_INT);
        $checkStmt->execute();
        $exists = $checkStmt->fetchColumn();
        
        if ($exists) {
            $error = "Row number $row_number already exists.";
        } else {
            // Update the record
            $sql = "UPDATE rowandprice SET row_number = :row_number, price = :price WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':row_number', $row_number);
            $stmt->bindValue(':price', $price);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            // Redirect to the list page
            header('Location: rowandprice_manage.php');
            exit();
        }
    }
}
?>

<?php include('header.php'); ?>

<!-- Main content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Row and Price</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <?php echo escape($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="post" action="">
                            <input type="hidden" name="_token" value="<?php echo $_SESSION['_token'] ?>">
                            <input type="hidden" name="_token" value="<?php echo $_SESSION["_token"];?>">
                            <div class="form-group">
                                <label for="row_number">Row Number</label>
                                <input type="text" class="form-control" id="row_number" name="row_number" value="<?php echo escape($row['row_number']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="price">Price</label>
                                <input type="text" class="form-control" id="price" name="price" value="<?php echo escape($row['price']); ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="rowandprice_manage.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content -->

<?php include('footer.html'); ?>
