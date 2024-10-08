<?php
session_start();
require '../config/config.php';
require '../config/common.php';

// Check if the user is logged in
if (empty($_SESSION['user_id']) || empty($_SESSION['login_time'])) {
    header('Location: login.php');
    exit();
}

// Handle actions based on query parameters
if (isset($_GET['action']) && isset($_GET['id'])) {
    $booking_id = $_GET['id'];
    $action = $_GET['action'];

    $actions = [
        'confirm' => 'confirmed',
        'cancel' => 'canceled',
        'delete' => null
    ];

    if (array_key_exists($action, $actions)) {
        if ($action === 'delete') {
            $sql = "DELETE FROM bookings WHERE id = :id";
        } else {
            $status = $actions[$action];
            $sql = "UPDATE bookings SET status = :status WHERE id = :id";
        }
        $stmt = $pdo->prepare($sql);
        $params = [':id' => $booking_id];
        if ($action !== 'delete') {
            $params[':status'] = $status;
        }
        $stmt->execute($params);
    }
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
$baseSql = "SELECT b.id, b.user_id, b.showtime_id, b.seat_id, b.booking_time, b.status, b.image, 
                   s.seat_number, s.row_number, h.name AS hall_name, m.name AS movie_name, 
                   st.start_time, st.end_time 
            FROM bookings b
            JOIN seats s ON b.seat_id = s.id
            JOIN showtimes st ON b.showtime_id = st.id
            JOIN halls h ON s.hall_id = h.id
            JOIN movies m ON st.movie_id = m.id";
if ($searchKey) {
    $baseSql .= " WHERE b.user_id LIKE :searchKey OR m.name LIKE :searchKey";
}

// Count total records for pagination
$countSql = $baseSql;
$countStmt = $pdo->prepare($countSql);
if ($searchKey) {
    $countStmt->bindValue(':searchKey', "%$searchKey%", PDO::PARAM_STR);
}
$countStmt->execute();
$totalRows = $countStmt->rowCount();
$totalPages = ceil($totalRows / $numberOfrec);

// Fetch records for the current page
$sql = $baseSql . " ORDER BY b.id DESC LIMIT :offset, :numberOfrec";
$stmt = $pdo->prepare($sql);
if ($searchKey) {
    $stmt->bindValue(':searchKey', "%$searchKey%", PDO::PARAM_STR);
}
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':numberOfrec', $numberOfrec, PDO::PARAM_INT);
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$id = $offset + 1;
?>

<?php include('header.php'); ?>
<!-- Main content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Booking Listings</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                    <div>
                            <a href="deleteAll.php"  onclick="return confirm('Are you sure you want to cancel this booking?')" type="button" class="btn btn-success">Delete All</a>
                        </div>
                        <br>
                        
                        <br>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>Username</th>
                                    <th>Movie Name</th>
                                    <th>Status</th>
                                    <th style="width: 180px">Actions</th>
                                    <th>View</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($bookings) {
                                    foreach ($bookings as $booking) {
                                        $sql = "SELECT email FROM users WHERE id = :id";
                                        $stmt = $pdo->prepare($sql);
                                        $stmt->execute([':id' => $booking['user_id']]);
                                        $user = $stmt->fetch(PDO::FETCH_ASSOC);
                                ?>
                                    <tr>
                                        <td><?php echo escape($id); ?></td>
                                        <td><?php echo escape($user['email']); ?></td>
                                        <td><?php echo escape($booking['movie_name']); ?></td>
                                        <td><?php echo escape($booking['status']); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="booking_manage.php?id=<?php echo escape($booking['id']); ?>&action=confirm" class="btn btn-success">Confirm</a>
                                                <a href="booking_manage.php?id=<?php echo escape($booking['id']); ?>&action=cancel" onclick="return confirm('Are you sure you want to cancel this booking?')" class="btn btn-danger">Cancel</a>
                                                <a href="booking_manage.php?id=<?php echo escape($booking['id']); ?>&action=delete" onclick="return confirm('Are you sure you want to delete this booking?')" class="btn btn-warning">Delete</a>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="booking_detail.php?id=<?php echo escape($booking['id']); ?>" class="btn btn-info">View Details</a>
                                        </td>
                                    </tr>
                                <?php
                                        $id++;
                                    }
                                } else {
                                    echo "<tr><td colspan='6'>No bookings found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                        <br>
                        <nav aria-label="Page navigation" style="float:right">
                            <ul class="pagination">
                                <li class="page-item <?php if ($pageno <= 1) { echo 'disabled'; } ?>">
                                    <a class="page-link" href="?pageno=1<?php echo $searchKey ? '&search=' . urlencode($searchKey) : ''; ?>">First</a>
                                </li>
                                <li class="page-item <?php if ($pageno <= 1) { echo 'disabled'; } ?>">
                                    <a class="page-link" href="<?php echo $pageno > 1 ? '?pageno=' . ($pageno - 1) . ($searchKey ? '&search=' . urlencode($searchKey) : '') : '#'; ?>">Previous</a>
                                </li>
                                <?php for ($page = 1; $page <= $totalPages; $page++) { ?>
                                    <li class="page-item <?php if ($pageno == $page) { echo 'active'; } ?>">
                                        <a class="page-link" href="?pageno=<?php echo $page; ?><?php echo $searchKey ? '&search=' . urlencode($searchKey) : ''; ?>"><?php echo $page; ?></a>
                                    </li>
                                <?php } ?>
                                <li class="page-item <?php if ($pageno >= $totalPages) { echo 'disabled'; } ?>">
                                    <a class="page-link" href="<?php echo $pageno < $totalPages ? '?pageno=' . ($pageno + 1) . ($searchKey ? '&search=' . urlencode($searchKey) : '') : '#'; ?>">Next</a>
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
