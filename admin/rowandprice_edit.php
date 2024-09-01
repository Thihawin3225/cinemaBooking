<?php
session_start();
require '../config/config.php';
require '../config/common.php';

// Check if the user is logged in
if (empty($_SESSION['user_id']) || empty($_SESSION['login_time'])) {
    header('Location: login.php');
    exit();
}

include('header.php');
?>

<!-- Main content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Row and Price Listings</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div>
                            <a href="rowandprice_add.php" type="button" class="btn btn-success">New Row and Price</a>
                        </div>
                        <br>
                        <?php
                        // Pagination setup
                        $pageno = isset($_GET['pageno']) ? (int)$_GET['pageno'] : 1;
                        $numberOfrec = 10;
                        $offset = ($pageno - 1) * $numberOfrec;

                        // Fetch total records for pagination
                        $rawsql = "SELECT * FROM rowandprice";
                        $rawstmt = $pdo->prepare($rawsql);
                        $rawstmt->execute();
                        $totalRows = $rawstmt->fetchAll();
                        $totalPages = ceil(count($totalRows) / $numberOfrec);

                        // Fetch records for the current page
                        $sql = "SELECT * FROM rowandprice LIMIT :offset, :numberOfrec";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                        $stmt->bindValue(':numberOfrec', $numberOfrec, PDO::PARAM_INT);
                        $stmt->execute();
                        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>Row Number</th>
                                    <th>Price</th>
                                    <th style="width: 40px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($rows) {
                                    $i = $offset + 1;
                                    foreach ($rows as $row) {
                                ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo escape($row['row_number']); ?></td>
                                    <td><?php echo escape($row['price']); ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="rowandprice_edit.php?id=<?php echo $row['id']; ?>" type="button" class="btn btn-warning">Edit</a>
                                            <a href="rowandprice_delete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this row price?')" type="button" class="btn btn-danger">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                    $i++;
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                        <br>
                        <nav aria-label="Page navigation example" style="float:right">
                            <ul class="pagination">
                                <li class="page-item <?php if ($pageno <= 1) { echo 'disabled'; } ?>">
                                    <a class="page-link" href="<?php if ($pageno > 1) { echo '?pageno=' . ($pageno - 1); } else { echo '#'; } ?>">Previous</a>
                                </li>
                                <li class="page-item"><a class="page-link" href="#"><?php echo $pageno; ?></a></li>
                                <li class="page-item <?php if ($pageno >= $totalPages) { echo 'disabled'; } ?>">
                                    <a class="page-link" href="<?php if ($pageno < $totalPages) { echo '?pageno=' . ($pageno + 1); } else { echo '#'; } ?>">Next</a>
                                </li>
                                <li class="page-item"><a class="page-link" href="?pageno=<?php echo $totalPages; ?>">Last</a></li>
                            </ul>
                        </nav>
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

<?php include('footer.html') ?>
 