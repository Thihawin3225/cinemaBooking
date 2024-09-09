<?php
session_start();
require './config/config.php';
require './config/common.php';

if (empty($_SESSION['userId']) || empty($_SESSION['loginTime'])) {
    header('Location: login.php');
    exit();
}

$hall_id = $_GET['hall_id'] ?? '';
$showtime_id = $_GET['showtime_id'] ?? '';

$sql = "SELECT m.id AS movie_id, m.name, m.description, m.release_date, m.duration, m.genre, m.rating, m.image, 
               s.id AS showtime_id, s.start_time, s.end_time, 
               h.id AS hall_id, h.name AS hall_name
        FROM movies m
        JOIN showtimes s ON m.id = s.movie_id
        JOIN halls h ON s.hall_id = h.id
        WHERE h.id = :hall_id AND s.id = :showtime_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':hall_id' => $hall_id, ':showtime_id' => $showtime_id]);
$movieDetails = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetching seat details with prices and statuses
// SQL query to fetch seat data
$sql = "SELECT s.id, s.seat_number, s.row_number, rp.price, b.status
        FROM seats s
        LEFT JOIN bookings b ON s.id = b.seat_id AND b.showtime_id = :showtime_id
        LEFT JOIN rowandprice rp ON s.row_number = rp.row_number
        WHERE s.hall_id = :hall_id
        ORDER BY s.row_number, s.seat_number";
        
// Prepare and execute the SQL statement
$stmt = $pdo->prepare($sql);
$stmt->execute([':hall_id' => $hall_id, ':showtime_id' => $showtime_id]);
$seats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Print the raw data for debugging

// Group seats by row number
$groupedSeats = [];
foreach ($seats as $seat) {
    // Check if the row number already exists in the array
    if (!isset($groupedSeats[$seat['row_number']])) {
        $groupedSeats[$seat['row_number']] = [];
    }
    // Add the seat to the appropriate row
    $groupedSeats[$seat['row_number']][] = $seat;
}

// Optional: Sort seats within each row by seat number if required
foreach ($groupedSeats as $rowNumber => &$seats) {
    usort($seats, function($a, $b) {
        return $a['seat_number'] - $b['seat_number'];
    });
}

// Optionally: Print the grouped and sorted data for verification



$today = date('Y-m-d'); // Format: YYYY-MM-DD

// Function to format movie dates
function formatDate($date) {
    global $today;
    // Convert the date to YYYY-MM-DD format for comparison
    $formattedDate = date('Y-m-d', strtotime($date));

    // Check if the date is today
    if ($formattedDate === $today) {
        return 'Today ' . date('g:i A', strtotime($date)); // Display time if today
    } else {
        return date('n/j/y g:i A', strtotime($date)); // Display date and time if not today
    }
}
$shortDescription = substr($movieDetails['description'], 0, 150); // Show only the first 150 characters
$fullDescription = $movieDetails['description'];
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
    <link rel="stylesheet" href="mo.css">
</head>
<style>
    .hidden{
        display: none;
    }
    .cinema-heading {
    width: 100%; /* Make the image responsive to the container width */
    max-width: 150px; /* Set a maximum width for the image */
    height: auto; /* Maintain the aspect ratio of the image */
    border-radius: 15px; /* Rounded corners for a smoother look */
    display: block; /* Ensure image is a block element for alignment */
}

</style>
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
                    <li><a href="logout.php">Logout</a></li>
                <?php } else { ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php } ?>
            </ul>
        </nav>
    <div class="container">
        <div class="movecontainer">
            <div class="left">
                <img src="./admin/<?php echo escape($movieDetails['image']); ?>" alt="Movie Poster">
            </div>
            <div class="right">
                <div class="card">
                    <div class="title"><span>Title: </span><?php echo escape($movieDetails['name']); ?></div>

                    <p>
    <span>Description:</span> 
    <span id="shortDescription"><?php echo $shortDescription; ?>...<a href="#" id="toggleDescription" onclick="toggleDescription(event)">See More</a></span>
    <span id="fullDescription" class="hidden"><?php echo $fullDescription; ?></span>
</p>

                    <div><span>Release Date:</span> <?php echo escape($movieDetails['release_date']); ?></div>
                    <div><span>Duration: </span><?php echo escape($movieDetails['duration']); ?> minutes</div>
                    <div><span>Rating: </span><?php echo escape($movieDetails['rating']); ?></div>
                    <div><span>Show Time:</span> <?php echo escape(formatDate($movieDetails['start_time'])); ?></div>
                    <div><span>End Time:</span> <?php echo escape(formatDate($movieDetails['end_time'])); ?></div>
                    <div><span>Hall:</span> <?php echo escape($movieDetails['hall_name']); ?></div>
                </div>
            </div>
        </div>

        <div class="seats">
            <div class="left" style="max-width: 1500px;">
                <form action="./admin/process_booking.php" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
                    <input type="hidden" name="_token" value="<?php echo $_SESSION['_token'] ?>">
                    <input type="hidden" name="hall_id" value="<?php echo escape($hall_id); ?>">
                    <input type="hidden" name="showtime_id" value="<?php echo escape($showtime_id); ?>">
                    <div class="screen">Screen</div>
                    <?php foreach ($groupedSeats as $row_number => $seatsInRow) { ?>
    <div class="row">
        <h1>Row <?php echo escape($row_number); ?></h1>
        <div class="seats-container" style="gap: 20px;">
            <div class="seats-left" style="display: flex;">
                <?php foreach (array_slice($seatsInRow, 0, ceil(count($seatsInRow) / 2)) as $seat) { ?>
                    <div class="seat">
                        <label for="seat_<?php echo $seat['id']; ?>"><?php echo escape($seat['seat_number']); ?></label>
                        <input type="checkbox" 
                               name="seat_ids[]" 
                               value="<?php echo escape($seat['id']); ?>" 
                               data-price="<?php echo escape($seat['price']); ?>"
                               id="seat_<?php echo $seat['id']; ?>" 
                               <?php if (!empty($seat['status']) && $seat['status'] != 'canceled') echo 'checked disabled'; ?>
                               onclick="calculateTotal()">
                    </div>
                <?php } ?>
            </div>

            <div class="seats-middle"></div> <!-- Middle gap -->

            <div class="seats-right" style="display: flex;">
                <?php foreach (array_slice($seatsInRow, ceil(count($seatsInRow) / 2)) as $seat) { ?>
                    <div class="seat">
                        <label for="seat_<?php echo $seat['id']; ?>"><?php echo escape($seat['seat_number']); ?></label>
                        <input type="checkbox" 
                               name="seat_ids[]" 
                               value="<?php echo escape($seat['id']); ?>" 
                               data-price="<?php echo escape($seat['price']); ?>"
                               id="seat_<?php echo $seat['id']; ?>" 
                               <?php if (!empty($seat['status']) && $seat['status'] != 'canceled') echo 'checked disabled'; ?>
                               onclick="calculateTotal()">
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>
<br>
                    <div class="kapay" style="text-align: center;">Kapay - 09690428503</div>
                    <div class="form-group mt-3 ">
                        <label for="image">Upload Your KBZ ScreenShot : </label>
                        <input type="file" name="image" id="image" class="form-control" style="width: 50%;">
                    </div>

                    <div class="form-group mt-3">
                        <button type="submit" class="btn btn-primary">Book Selected Seats</button>
                    </div>
                </form>
            </div>

            <div class="right">
                <div class="rdetail">
                   <div>
                    <div class="screen">Summary</div>
                   <?php foreach ($groupedSeats as $row_number => $seatsInRow) { ?>
                        <div class="row"><span class="rpc">Row <?php echo escape($row_number); ?>  $<?php echo escape($seatsInRow[0]['price']); ?></span></div>
                    <?php } ?>
                   </div>
                    <div class="seat-de">
                    <div>Please Select Seat</div>
                    <div class="bookseatSelectDetail">
                        <div>Movie Name: <?php echo escape($movieDetails['name']); ?></div>
                        <div>Seat number is: <span id="selected-seats"></span></div>
                        <div class="totalPrice">Total Price - $<span id="total-cost">0</span></div>
                    </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="error-message" style="color: red; display: none;"></div>
    </div>

    <script>
function calculateTotal() {
    let checkboxes = document.querySelectorAll('input[name="seat_ids[]"]:checked:not([disabled])');
    let totalCost = 0;
    let selectedSeats = [];

    checkboxes.forEach(function(checkbox) {
        let seatContainer = checkbox.closest('.seat');
        let seatLabel = seatContainer.querySelector('label').textContent;
        let rowNumber = checkbox.closest('.row').querySelector('h1').textContent.split(' ')[1];
        let seatNumber = seatLabel.split(' ')[0];
        
        totalCost += parseFloat(checkbox.getAttribute('data-price'));
        selectedSeats.push('Row ' + rowNumber + ' - Seat ' + seatNumber);
    });

    document.querySelector('.totalPrice span').textContent = totalCost.toFixed(2);
    document.getElementById('selected-seats').textContent = selectedSeats.join(', ');
}

function validateForm() {
    var checkboxes = document.querySelectorAll('input[name="seat_ids[]"]:checked');
    var image = document.getElementById('image').value;
    var errorMessage = '';

    // Check if at least one seat is selected
    if (checkboxes.length === 0) {
        errorMessage += 'You must select at least one seat.\n';
    }

    // Check if image is uploaded
    if (image === '') {
        errorMessage += 'You must upload an image.\n';
    }

    // If there are errors, display them using alert and prevent form submission
    if (errorMessage !== '') {
        alert(errorMessage.trim()); // Use trim() to remove any trailing newlines
        return false; // Prevent form submission
    }

    // Allow form submission if no errors
    return true;
}
function toggleDescription(event) {
    event.preventDefault(); // Prevent the default action of the link
    
    var shortDescription = document.getElementById('shortDescription');
    var fullDescription = document.getElementById('fullDescription');
    var toggleLink = document.getElementById('toggleDescription');
    
    if (fullDescription.classList.contains('hidden')) {
        // Show full description and hide short description
        fullDescription.classList.remove('hidden');
        shortDescription.style.display = 'none';
        toggleLink.textContent = 'See Less'; // Change link text to "See Less"
    } else {
        // Show short description and hide full description
        fullDescription.classList.add('hidden');
        shortDescription.style.display = 'inline';
        toggleLink.textContent = 'See More'; // Change link text to "See More"
    }
}

</script>

</body>
</html>
