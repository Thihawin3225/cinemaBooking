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

    // Filter by selected days of the week
    if (!empty($_GET['week'])) {
        $weekDays = array_map(function ($day) use ($pdo) {
            return $pdo->quote($day);
        }, $_GET['week']);
        $weekDaysList = implode(',', $weekDays);
        $filterClauses[] = "DAYNAME(s.start_time) IN ($weekDaysList)";
    }

    // Construct the filter SQL query
    $filterSql = '';
    if (!empty($filterClauses)) {
        $filterSql = ' AND ' . implode(' AND ', $filterClauses);
    }

    // Fetch movies with showtimes and hall details
    $sql = "SELECT m.id AS movie_id, m.name, m.description, m.release_date, m.duration, m.genre, m.rating, m.image, 
               s.id AS showtime_id, s.start_time, s.end_time, 
               h.id AS hall_id, h.name AS hall_name
        FROM movies m
        JOIN showtimes s ON m.id = s.movie_id
        JOIN halls h ON s.hall_id = h.id
        WHERE s.start_time >= CURDATE() 
        $filterSql
        ORDER BY s.start_time Asc";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch distinct genres for the filter sidebar
    $genreSql = "SELECT DISTINCT genre FROM movies";
    $genreStmt = $pdo->prepare($genreSql);
    $genreStmt->execute();
    $genres = $genreStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all halls for the filter sidebar
    $hallSql = "SELECT * FROM halls";
    $hallStmt = $pdo->prepare($hallSql);
    $hallStmt->execute();
    $halls = $hallStmt->fetchAll(PDO::FETCH_ASSOC);
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
        /* Style for the cinema heading image */
.cinema-heading {
    width: 100%; /* Make the image responsive to the container width */
    max-width: 150px; /* Set a maximum width for the image */
    height: auto; /* Maintain the aspect ratio of the image */
    border-radius: 15px; /* Rounded corners for a smoother look */
    display: block; /* Ensure image is a block element for alignment */
}

.simage{
    box-sizing: border-box;
    height: 240px;
    display: block;
    filter: brightness(1);
    transition: filter .3s ease-in
}



/* Optional: Add responsive design for smaller screens */
@media (max-width: 600px) {
    .cinema-heading {
        font-size: 2rem; /* Smaller font size on mobile */
        padding: 5px; /* Reduced padding */
    }
}
.slide_custom {
    padding: 2rem;
}
.carousel-inner{
    border-radius: 20px;
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
                    <li><a href="./booking_success.php"><?php echo escape($_SESSION['userName']); ?></a></li>
                    <li><a href="ulogout.php">Logout</a></li>
                <?php } else { ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php } ?>
            </ul>
        </nav>
        <div id="carouselExample" class="carousel slide slide_custom">
  <div class="carousel-inner">
    <div class="carousel-item active">
    <img src="./sliderimage/one.jpg" class="d-block w-100" alt="...">
    </div>
    <div class="carousel-item">
    <img src="./sliderimage/two.jpg" class="d-block w-100" alt="...">
    </div>
    <div class="carousel-item">
    <img src="./sliderimage/three.jpg" class="d-block w-100" alt="...">
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>
        <h1 class="category">Select Categories</h1>
        <div class="container">
            <div class="cleft">
                    <form method="GET" action="">
                        <button id="hallButton" type="button" class="cbtn">Hall <i class="bi bi-chevron-down"></i></button>
                        <div id="hallContainer">
                            <div class="filter-group">
                                <?php foreach ($halls as $hall) { ?>
                                    <div class="hall">
                                        <input type="checkbox" id="hall_<?php echo $hall['id']; ?>" name="hall[]" value="<?php echo $hall['id']; ?>" <?php echo isset($_GET['hall']) && in_array($hall['id'], $_GET['hall']) ? 'checked' : ''; ?>>
                                        <label for="hall_<?php echo $hall['id']; ?>"><?php echo escape($hall['name']); ?></label><br>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <button id="genreButton" type="button" class="cbtn">Genre <i class="bi bi-chevron-down"></i></button>
                        <div id="genreContainer" class="btn">
                            <div class="filter-group">
                                <?php foreach ($genres as $genre) { ?>
                                    <div class="genre">
                                        <input type="checkbox" id="genre_<?php echo escape($genre['genre']); ?>" name="genre[]" value="<?php echo escape($genre['genre']); ?>"  <?php echo isset($_GET['genre']) && in_array($genre['genre'], $_GET['genre']) ? 'checked' : ''; ?>>
                                        <label for="genre_<?php echo escape($genre['genre']); ?>"><?php echo escape($genre['genre']); ?></label><br>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <!-- Add this new section for the "Weeks" filter in your form -->
                        <button id="weekButton" type="button" class="cbtn">Week <i class="bi bi-chevron-down"></i></button>
                        <div id="weekContainer" class="btn">
                            <div class="filter-group">
                                <?php
                                    $daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                    foreach ($daysOfWeek as $day) { ?>
                                <div class="week">
                                    <input type="checkbox" id="week_<?php echo $day; ?>" name="week[]" value="<?php echo $day; ?>" <?php echo isset($_GET['week']) && in_array($day, $_GET['week']) ? 'checked' : ''; ?>>
                                    <label for="week_<?php echo $day; ?>"><?php echo $day; ?></label><br>
                                </div>
                                <?php } ?>
                            </div>
                        </div>

                    </form>
            </div>

            <div class="right">
                <?php if (!empty($movies)) { ?>
                    <?php foreach ($movies as $movie) { ?>
                        <div class="movie-card">
                            <img src="./admin/<?php echo escape($movie['image']); ?>" alt="<?php echo escape($movie['name']); ?>" class="movie-poster">
                            <div class="card-detail">
                                <div class="movie-rating">
                                <h3 class="movie-name"><?php echo escape($movie['name']); ?></h3>
                                <span><i class="bi bi-star-fill"></i> <?php echo escape($movie['rating']); ?></span>
                                </div>
                                <div class="movie-dates">
                                    <p>Start Date: <span><?php echo escape(formatDate($movie['start_time'])); ?></span></p>
                                    <p>End Date: <span><?php echo escape(formatDate($movie['end_time'])); ?></span></p>
                                </div>
                            </div>
                            <?php
// Initialize DateTime objects with proper time zone

$timezone = new DateTimeZone('Asia/Yangon'); 
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
                    <p>No movies found matching your criteria.</p>
                <?php } ?>
            </div>
        </div>
        <?php include('footer.html') ?>
        </div>
        

</body>

</html>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Automatically show the hall, genre, and week filter containers
        document.getElementById("hallContainer").style.display = "block";
        document.getElementById("genreContainer").style.display = "block";
        document.getElementById("weekContainer").style.display = "block";

        // Attach event listeners to hall checkboxes
        const hallCheckboxes = document.querySelectorAll('input[name="hall[]"]');
        hallCheckboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                applyFilters();
            });
        });

        // Attach event listeners to genre checkboxes
        const genreCheckboxes = document.querySelectorAll('input[name="genre[]"]');
        genreCheckboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                applyFilters();
            });
        });

        // Attach event listeners to week checkboxes
        const weekCheckboxes = document.querySelectorAll('input[name="week[]"]');
        weekCheckboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                applyFilters();
            });
        });

        // Function to submit the form when filters are selected
        function applyFilters() {
            const form = document.querySelector('form');
            form.submit();
        }
    });
</script>

