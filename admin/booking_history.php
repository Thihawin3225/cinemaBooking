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
$searchKey = isset($_POST['search']) ? trim($_POST['search']) : (isset($_COOKIE['search']) ? $_COOKIE['search'] : '');
$searchKey = escape($searchKey, ENT_QUOTES, 'UTF-8'); // Sanitize input
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    setcookie('search', $searchKey, time() + (86400 * 30), "/");
} else {
    if (empty($_GET['pageno'])) {
        setcookie('search', '', time() - 3600, '/'); // Clear cookie
    }
}

// Pagination setup
$pageno = isset($_GET['pageno']) ? (int)$_GET['pageno'] : 1;
$numberOfrec = 10; // Records per page
$offset = ($pageno - 1) * $numberOfrec;

// Base SQL query with search condition
$baseSql = "SELECT b.status, COUNT(b.id) AS count, m.name AS movie_name, st.start_time 
            FROM bookings b
            JOIN showtimes st ON b.showtime_id = st.id
            JOIN movies m ON st.movie_id = m.id 
            GROUP BY b.status, m.name, st.start_time";
if ($searchKey) {
    $baseSql .= " HAVING m.name LIKE :searchKey";
}

// Count total records for pagination
$countSql = $baseSql;
$countStmt = $pdo->prepare($countSql);
if ($searchKey) {
    $countStmt->bindValue(':searchKey', "%$searchKey%", PDO::PARAM_STR);
}
$countStmt->execute();
$totalRows = $countStmt->rowCount(); // Use rowCount for count with search
$totalPages = ceil($totalRows / $numberOfrec);

// Fetch records for the current page
$sql = $baseSql . " LIMIT :offset, :numberOfrec";
$stmt = $pdo->prepare($sql);
if ($searchKey) {
    $stmt->bindValue(':searchKey', "%$searchKey%", PDO::PARAM_STR);
}
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':numberOfrec', $numberOfrec, PDO::PARAM_INT);
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include('header.php'); ?>
<!-- Main content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Booking History</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Movie Name</th>
                                    <th>Start Time</th>
                                    <th>Status</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($bookings) {
                                    foreach ($bookings as $booking) {
                                ?>
                                    <tr>
                                        <td><?php echo escape($booking['movie_name']); ?></td>
                                        <td><?php echo escape((new DateTime($booking['start_time']))->format('d/m/Y g:i A')); ?></td>
                                        <td><?php echo escape($booking['status']); ?></td>
                                        <td><?php echo escape($booking['count']); ?></td>
                                    </tr>
                                <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='4'>No bookings found</td></tr>";
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
<?php include('footer.html'); ?>
