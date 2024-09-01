<?php
session_start();
require '../config/config.php';
require '../config/common.php';

// Check if the user is logged in
if (empty($_SESSION['user_id']) || empty($_SESSION['login_time'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: booking_manage.php');
    exit();
}

$booking_id = $_GET['id'];
$booking_id = escape($booking_id, ENT_QUOTES, 'UTF-8'); // Sanitize input

$sql = "SELECT b.*, s.seat_number, s.row_number, h.name AS hall_name, m.name AS movie_name, 
               st.start_time, st.end_time, u.email
        FROM bookings b
        JOIN seats s ON b.seat_id = s.id
        JOIN showtimes st ON b.showtime_id = st.id
        JOIN halls h ON s.hall_id = h.id
        JOIN movies m ON st.movie_id = m.id
        JOIN users u ON b.user_id = u.id
        WHERE b.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $booking_id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    header('Location: booking_manage.php');
    exit();
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
                        <h3 class="card-title">Booking Details</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-3">Username</dt>
                            <dd class="col-sm-9"><?php echo escape($booking['email']); ?></dd>

                            <dt class="col-sm-3">Movie Name</dt>
                            <dd class="col-sm-9"><?php echo escape($booking['movie_name']); ?></dd>

                            <dt class="col-sm-3">Hall</dt>
                            <dd class="col-sm-9"><?php echo escape($booking['hall_name']); ?></dd>

                            <dt class="col-sm-3">Seat Number</dt>
                            <dd class="col-sm-9"><?php echo escape($booking['seat_number']); ?></dd>

                            <dt class="col-sm-3">Row Number</dt>
                            <dd class="col-sm-9"><?php echo escape($booking['row_number']); ?></dd>

                            <dt class="col-sm-3">Showtime Start</dt>
                            <dd class="col-sm-9"><?php echo escape((new DateTime($booking['start_time']))->format('d/m/Y g:i A')); ?></dd>

                            <dt class="col-sm-3">Showtime End</dt>
                            <dd class="col-sm-9"><?php echo escape((new DateTime($booking['end_time']))->format('d/m/Y g:i A')); ?></dd>

                            <dt class="col-sm-3">Booking Time</dt>
                            <dd class="col-sm-9"><?php echo escape((new DateTime($booking['booking_time']))->format('d/m/Y g:i A')); ?></dd>

                            <dt class="col-sm-3">Status</dt>
                            <dd class="col-sm-9"><?php echo escape($booking['status']); ?></dd>

                            <dt class="col-sm-3">Image</dt>
                            <dd class="col-sm-9">
                                <img src="<?php echo escape($booking['image']); ?>" alt="Booking Image" style="width:300px;height:300px;">
                            </dd>
                        </dl>
                        <a href="booking_manage.php" class="btn btn-primary">Back to Booking List</a>
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
