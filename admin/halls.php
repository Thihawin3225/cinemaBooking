<?php
session_start();
require '../config/config.php';
require '../config/common.php';

// Check if the user is logged in
if (empty($_SESSION['user_id']) || empty($_SESSION['login_time'])) {
    header('Location: login.php');
    exit();
}

// Handle search query
$searchKey = isset($_POST['search']) ? trim($_POST['search']) : '';
$searchKey = htmlspecialchars($searchKey, ENT_QUOTES, 'UTF-8'); // Sanitize input
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    setcookie('search', $searchKey, time() + (86400 * 30), "/");
} else {
    if (empty($_GET['pageno'])) {
        setcookie('search', '', time() - 3600, '/'); // Clear cookie
    }
}

// Fetch the search term from cookie
$searchKey = isset($_COOKIE['search']) ? $_COOKIE['search'] : $searchKey;
?>
<?php include('header.php') ?>
<!-- Main content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Hall Listings</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div>
                            <a href="hall_add.php" type="button" class="btn btn-success">New Hall</a>
                        </div>
                        <br>
                        
                       
                        <br>
                       
                        <?php
                        // Pagination setup
                        $pageno = isset($_GET['pageno']) ? (int)$_GET['pageno'] : 1;
                        $numOfrecs = 10;
                        $offset = ($pageno - 1) * $numOfrecs;

                        // Base SQL query
                        $baseSql = "SELECT * FROM Halls";
                        if ($searchKey) {
                            $baseSql .= " WHERE name LIKE :searchKey";
                        }

                        // Count total records for pagination
                        $countSql = $baseSql;
                        $countStmt = $pdo->prepare($countSql);
                        if ($searchKey) {
                            $countStmt->bindValue(':searchKey', "%$searchKey%", PDO::PARAM_STR);
                        }
                        $countStmt->execute();
                        $totalCount = $countStmt->rowCount();
                        $totalPages = ceil($totalCount / $numOfrecs);

                        // Fetch records for the current page
                        $pagedSql = $baseSql . " LIMIT :offset, :numOfrecs";
                        $stmt = $pdo->prepare($pagedSql);
                        if ($searchKey) {
                            $stmt->bindValue(':searchKey', "%$searchKey%", PDO::PARAM_STR);
                        }
                        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                        $stmt->bindValue(':numOfrecs', $numOfrecs, PDO::PARAM_INT);
                        $stmt->execute();
                        $halls = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        $id = $offset + 1;
                        ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>Name</th>
                                    <th>Capacity</th>
                                    <th style="width: 40px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($halls) {
                                    foreach ($halls as $hall) {
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($id); ?></td>
                                    <td><?php echo htmlspecialchars($hall['name']); ?></td>
                                    <td><?php echo htmlspecialchars($hall['capacity']); ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="hall_edit.php?id=<?php echo htmlspecialchars($hall['id']); ?>" type="button" class="btn btn-warning">Edit</a>
                                            <a href="hall_delete.php?id=<?php echo htmlspecialchars($hall['id']); ?>"
                                               onclick="return confirm('Are you sure you want to delete this hall?')"
                                               type="button" class="btn btn-danger">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                        $id++;
                                    }
                                } else {
                                    echo "<tr><td colspan='4'>No records found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                        <br>
                        <nav aria-label="Page navigation example" style="float:right">
                            <ul class="pagination">
                                <li class="page-item <?php if ($pageno <= 1) { echo 'disabled'; } ?>">
                                    <a class="page-link" href="?pageno=1<?php echo $searchKey ? '&search=' . urlencode($searchKey) : ''; ?>">First</a>
                                </li>
                                <li class="page-item <?php if ($pageno <= 1) { echo 'disabled'; } ?>">
                                    <a class="page-link" href="<?php if ($pageno > 1) { echo '?pageno=' . ($pageno - 1) . ($searchKey ? '&search=' . urlencode($searchKey) : ''); } else { echo '#'; } ?>">Previous</a>
                                </li>
                                <?php for ($page = 1; $page <= $totalPages; $page++) { ?>
                                    <li class="page-item <?php if ($pageno == $page) { echo 'active'; } ?>">
                                        <a class="page-link" href="?pageno=<?php echo $page; ?><?php echo $searchKey ? '&search=' . urlencode($searchKey) : ''; ?>"><?php echo $page; ?></a>
                                    </li>
                                <?php } ?>
                                <li class="page-item <?php if ($pageno >= $totalPages) { echo 'disabled'; } ?>">
                                    <a class="page-link" href="<?php if ($pageno < $totalPages) { echo '?pageno=' . ($pageno + 1) . ($searchKey ? '&search=' . urlencode($searchKey) : ''); } else { echo '#'; } ?>">Next</a>
                                </li>
                                <li class="page-item <?php if ($pageno >= $totalPages) { echo 'disabled'; } ?>">
                                    <a class="page-link" href="?pageno=<?php echo $totalPages; ?><?php echo $searchKey ? '&search=' . urlencode($searchKey) : ''; ?>">Last</a>
                                </li>
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
