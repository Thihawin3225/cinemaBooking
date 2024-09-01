<?php
session_start();
require 'config/config.php';
require 'config/common.php';

try {
    // Initialize filter clauses and parameters
    $filterClauses = [];
    $params = [];

    // Filter by selected halls
    if (!empty($_GET['hall'])) {
        $hallIds = implode(',', array_map('intval', $_GET['hall']));
        $filterClauses[] = "h.id IN ($hallIds)";
    }

    // Filter by selected genres
    if (!empty($_GET['genre'])) {
        $genres = implode(',', array_map(function ($genre) use ($pdo) {
            return $pdo->quote($genre);
        }, $_GET['genre']));
        $filterClauses[] = "m.genre IN ($genres)";
    }

    // Construct the filter SQL query
    $filterSql = '';
    if (!empty($filterClauses)) {
        $filterSql = ' WHERE ' . implode(' AND ', $filterClauses);
    }

    // Get today's date in Asia/Yangon timezone
    $timezone = new DateTimeZone('Asia/Yangon');
    $today = new DateTime('now', $timezone);
    $todayDate = $today->format('Y-m-d');

    // Fetch movies with showtimes and hall details
    $sql = "SELECT m.id AS movie_id, m.name, m.description, m.release_date, m.duration, m.genre, m.rating, m.image, 
                   s.id AS showtime_id, s.start_time, s.end_time, 
                   h.id AS hall_id, h.name AS hall_name
            FROM movies m
            JOIN showtimes s ON m.id = s.movie_id
            JOIN halls h ON s.hall_id = h.id
            $filterSql";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $allMovies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Separate movies into Now Showing and Coming Soon
    $nowShowingMovies = [];
    $comingSoonMovies = [];
    
    foreach ($allMovies as $movie) {
        $startDateTime = new DateTime($movie['start_time'], new DateTimeZone('UTC'));
        $startDateTime->setTimezone($timezone);
        $startDate = $startDateTime->format('Y-m-d');

        if ($startDate === $todayDate) {
            $nowShowingMovies[] = $movie;
        } elseif ($startDate > $todayDate) {
            $comingSoonMovies[] = $movie;
        }
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    exit();
}

// Function to format movie dates
function formatDate($date) {
    $timezone = new DateTimeZone('Asia/Yangon');
    $dateTime = new DateTime($date, $timezone);
    $dateTime->setTimezone($timezone);
    $todayDate = (new DateTime('now', $timezone))->format('Y-m-d');
    
    $formattedDate = $dateTime->format('Y-m-d');
    if ($formattedDate === $todayDate) {
        return 'Today ' . $dateTime->format('g:i A');
    } else {
        return $dateTime->format('n/j/y g:i A');
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
                    <li><a href="./booking_success.php"><?php echo htmlspecialchars($_SESSION['userName']); ?></a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php } else { ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php } ?>
            </ul>
        </nav>

        <h1 class="category">Now Showing</h1>

        <div class="container">
            <div class="row">
                <?php if (!empty($nowShowingMovies)) { ?>
                    <?php foreach ($nowShowingMovies as $movie) { ?>
                            <div class="movie-card">
                                <img src="./admin/<?php echo htmlspecialchars($movie['image']); ?>" alt="<?php echo htmlspecialchars($movie['name']); ?>" class="movie-poster">
                                <div class="card-detail">
                                    <div class="movie-rating">
                                        <h3 class="movie-name"><?php echo htmlspecialchars($movie['name']); ?></h3>
                                        <span><?php echo htmlspecialchars($movie['rating']); ?></span>
                                    </div>
                                    <div class="movie-dates">
                                        <p>Start Date: <span><?php echo htmlspecialchars(formatDate($movie['start_time'])); ?></span></p>
                                        <p>End Date: <span><?php echo htmlspecialchars(formatDate($movie['end_time'])); ?></span></p>
                                    </div>
                                </div>
                                <a href="movedetail.php?showtime_id=<?php echo $movie['showtime_id']; ?>&hall_id=<?php echo $movie['hall_id']; ?>" class="btn btn-primary">Book Now</a>
                            </div>
                    <?php } ?>
                <?php } else { ?>
                    <p>No movies are currently showing.</p>
                <?php } ?>
            </div>
        </div>
        
        <h1 class="category">Coming Soon</h1>
           <div class="container">
           <div class="row">
                <?php if (!empty($comingSoonMovies)) { ?>
                    <?php foreach ($comingSoonMovies as $movie) { ?>
                            <div class="movie-card">
                                <img src="./admin/<?php echo htmlspecialchars($movie['image']); ?>" alt="<?php echo htmlspecialchars($movie['name']); ?>" class="movie-poster">
                                <div class="card-detail">
                                    <div class="movie-rating">
                                        <h3 class="movie-name"><?php echo htmlspecialchars($movie['name']); ?></h3>
                                        <span><?php echo htmlspecialchars($movie['rating']); ?></span>
                                    </div>
                                    <div class="movie-dates">
                                        <p>Start Date: <span><?php echo htmlspecialchars(formatDate($movie['start_time'])); ?></span></p>
                                        <p>End Date: <span><?php echo htmlspecialchars(formatDate($movie['end_time'])); ?></span></p>
                                    </div>
                                </div>
                                <a href="movedetail.php?showtime_id=<?php echo $movie['showtime_id']; ?>&hall_id=<?php echo $movie['hall_id']; ?>" class="btn btn-primary">Book Now</a>
                            </div>
                    <?php } ?>
                <?php } else { ?>
                    <p>No movies coming soon.</p>
                <?php } ?>
            </div>
           </div>

        <div class="footer">
            Lorem ipsum dolor sit amet consectetur adipisicing elit.
        </div>
    </div>
</body>

</html>
