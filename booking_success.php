<?php
session_start();
require './config/config.php';
require './config/common.php';

// Check if the user is logged in
if (empty($_SESSION['userId']) || empty($_SESSION['loginTime'])) {
    header('Location: ../login.php');
    exit();
}

// Get current user ID
$user_id = $_SESSION['userId'];

// SQL query to fetch booking details for the logged-in user
$sql = "SELECT b.id, b.booking_time, b.status, b.image, 
               s.seat_number, s.row_number, rp.price, h.name AS hall_name, 
               m.name AS movie_name, m.image As move_image, st.start_time, st.end_time
        FROM bookings b
        JOIN seats s ON b.seat_id = s.id
        JOIN rowandprice rp ON s.row_number = rp.row_number
        JOIN halls h ON s.hall_id = h.id
        JOIN showtimes st ON b.showtime_id = st.id
        JOIN movies m ON st.movie_id = m.id
        WHERE b.user_id = ? ORDER BY b.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get the current date and time in Asia/Yangon timezone
$now = new DateTime('now', new DateTimeZone('Asia/Yangon'));
$today = $now->format('Y-m-d');

// Function to format movie dates
function formatDate($date) {
    global $today;
    
    // Convert the date to Asia/Yangon timezone and format it
    $dateTime = new DateTime($date, new DateTimeZone('Asia/Yangon')); // Assuming the date is in UTC
    $dateTime->setTimezone(new DateTimeZone('Asia/Yangon'));
    $formattedDate = $dateTime->format('Y-m-d');

    // Check if the date is today
    if ($formattedDate === $today) {
        return 'Today ' . $dateTime->format('g:i A'); // Display time if today
    } else {
        return $dateTime->format('n/j/y g:i A'); // Display date and time if not today
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema Booking System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="move.css">
    <style>
        @media (min-width: 992px) {
            .container, .container-lg, .container-md, .container-sm {
                max-width: 100vw !important;
            }
        }
    </style>
</head>
<body>
    <div class="mainContainer">
        <nav class="nav-bar">
            <h1>Cinema Booking</h1>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="move.php">Movies</a></li>
                <li><a href="#">Contact us</a></li>
            </ul>
            <ul>
                <?php if (!empty($_SESSION['userName'])) { ?>
                    <li><a href="./booking_success.php"><?php echo escape($_SESSION['userName']); ?></a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php } else { ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php } ?>
            </ul>
        </nav>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">My Bookings</h3>
                            </div>
                            <div class="card-body">
                                <?php if (count($bookings) > 0) { ?>
                                    <h4>Booking Details:</h4>
                                    <div class="row">
                                        <?php foreach ($bookings as $booking) { ?>
                                            <div class="col-md-4 mb-3">
                                                <div class="card">
                                                    <img src="./admin/<?php echo escape($booking['move_image']); ?>" style="height: 280px;" alt="Booking Image" class="card-img-top img-fluid">
                                                    <div class="card-body">
                                                        <h5 class="card-title"><?php echo escape($booking['movie_name']); ?></h5>
                                                        <p class="card-text">Hall: <?php echo escape($booking['hall_name']); ?></p>
                                                        <p class="card-text">Seat: <?php echo escape($booking['seat_number']); ?> (Row <?php echo escape($booking['row_number']); ?>)</p>
                                                        <p class="card-text">Price: $<?php echo escape($booking['price']); ?></p>
                                                        <p class="card-text">Showtime: <?php echo escape(formatDate($booking['start_time'])); ?> - <?php echo escape(formatDate($booking['end_time'])); ?></p>
                                                        <p class="card-text">Booking Time: <?php echo escape(($booking['booking_time'])); ?></p>
                                                        <p class="card-text"><strong>Status:</strong> <?php echo escape($booking['status']); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } else { ?>
                                    <p>No bookings found.</p>
                                <?php } ?>
                                <a href="../index.php" class="btn btn-primary mt-3">Back to Home</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
