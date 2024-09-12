
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
     
    $timezone = new DateTimeZone('Asia/Yangon'); // e.g., 'America/New_York', 'Europe/Berlin'
    // Get today's date in default timezone
    $today = new DateTime('now',$timezone);
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
    $allMovies = $stmt->fetchAll();

    // Separate movies into Now Showing and Coming Soon
    $nowShowingMovies = [];
    $comingSoonMovies = [];
    
    foreach ($allMovies as $movie) {
        $startDateTime = new DateTime($movie['start_time']);
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
function formatDate($date, $timezone = 'Asia/Yangon') {
    $dateTime = new DateTime($date, new DateTimeZone($timezone));
    $todayDate = (new DateTime('now', new DateTimeZone($timezone)))->format('Y-m-d');
    
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
    <link rel="stylesheet" href="movies.css">

    <style>
        @media (min-width: 992px) {
            .container, .container-lg, .container-md, .container-sm {
                max-width: 100vw !important;
            }
        }
        .cinema-heading {
    width: 100%; /* Make the image responsive to the container width */
    max-width: 150px; /* Set a maximum width for the image */
    height: auto; /* Maintain the aspect ratio of the image */
    border-radius: 15px; /* Rounded corners for a smoother look */
    display: block; /* Ensure image is a block element for alignment */
}

    </style>
</head>

<body>
    <div class="mainContainer">
        <nav class="nav-bar">
        <img src="./images/Screenshot_2024-09-08_163828-removebg-preview.png" class="cinema-heading" alt="">           
         <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="move.php">Movies</a></li>
                <li><a href="./contactus/index.php">Contact us</a></li>
            </ul>
            <ul>
                <?php if (!empty($_SESSION['userName'])) { ?>
                    <li><a href="./booking_success.php"><?php echo htmlspecialchars($_SESSION['userName']); ?></a></li>
                    <li><a href="ulogout.php">Logout</a></li>
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
                                <?php
// Initialize DateTime objects with proper time zone

$timezone = new DateTimeZone('Asia/Yangon'); // e.g., 'America/New_York', 'Europe/Berlin'
$today = new DateTime('now', $timezone);
$currentTimestamp = $today->getTimestamp(); // Get current timestamp
// Example movie start time
$movieStartTimeStr = $movie['start_time']; // Ensure this is in a valid DateTime format
$startDateTime = new DateTime($movieStartTimeStr, $timezone);
$startTimestamp = $startDateTime->getTimestamp(); // Get movie start timestamp

// Compare timestamps and display appropriate button
if ($startTimestamp >= $currentTimestamp) {
    // Movie start time is in the past or now
    ?>
    <a href="movedetail.php?showtime_id=<?php echo htmlspecialchars($movie['showtime_id']); ?>&hall_id=<?php echo htmlspecialchars($movie['hall_id']); ?>" class="btn btn-primary">Book Now</a>
    <?php
} else {
    // Movie start time is in the future
    ?>
    <a href="" class="btn btn-secondary">Close</a>
    <?php
}
?>

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

        
    </div>
    <?php include('footer.html') ?>

</body>

</html>
