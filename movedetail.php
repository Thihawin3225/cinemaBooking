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

// SQL query to fetch movie details
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

// SQL query to fetch seat details
$sql = "SELECT s.id, s.seat_number, s.row_number, rp.price, b.status
        FROM seats s
        LEFT JOIN bookings b ON s.id = b.seat_id AND b.showtime_id = :showtime_id
        LEFT JOIN rowandprice rp ON s.row_number = rp.row_number
        WHERE s.hall_id = :hall_id
        ORDER BY s.row_number, s.seat_number";
$stmt = $pdo->prepare($sql);
$stmt->execute([':hall_id' => $hall_id, ':showtime_id' => $showtime_id]);
$seats = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total = count($seats);

// Group seats by row number
$groupedSeats = [];
foreach ($seats as $seat) {
    if (!isset($groupedSeats[$seat['row_number']])) {
        $groupedSeats[$seat['row_number']] = [];
    }
    $groupedSeats[$seat['row_number']][] = $seat;
}

// Sort seats within each row by seat number
foreach ($groupedSeats as $rowNumber => &$seats) {
    usort($seats, function($a, $b) {
        return $a['seat_number'] - $b['seat_number'];
    });
}

// Function to format movie dates
function formatDate($date) {
    $today = date('Y-m-d');
    $formattedDate = date('Y-m-d', strtotime($date));

    if ($formattedDate === $today) {
        return 'Today ' . date('g:i A', strtotime($date));
    } else {
        return date('n/j/y g:i A', strtotime($date));
    }
}
$shortDescription = substr($movieDetails['description'], 0, 150);
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
    <link rel="stylesheet" href="moc.css">
    <style>
        .hidden {
            display: none;
        }
        .cinema-heading {
            width: 100%;
            max-width: 150px;
            height: auto;
            border-radius: 15px;
            display: block;
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
                    <img src="./admin/<?php echo htmlspecialchars($movieDetails['image']); ?>" alt="Movie Poster">
                </div>
                <div class="right">
                    <div class="card">
                        <div class="title"><span>Title: </span><?php echo htmlspecialchars($movieDetails['name']); ?></div>
                        <p>
    <span>Description:</span> 
    <span id="shortDescription"><?php echo htmlspecialchars($shortDescription); ?>...<a href="#" id="toggleDescription" onclick="toggleDescription(event)">See More</a></span>
    <span id="fullDescription" class="hidden"><?php echo htmlspecialchars($fullDescription); ?> <a href="#" id="toggleDescription" onclick="toggleDescription(event)">See Less</a></span>
</p>

                        <div><span>Release Date:</span> <?php echo htmlspecialchars($movieDetails['release_date']); ?></div>
                        <div><span>Duration: </span><?php echo htmlspecialchars($movieDetails['duration']); ?> minutes</div>
                        <div><span>Rating: </span><?php echo htmlspecialchars($movieDetails['rating']); ?></div>
                        <div><span>Show Time:</span> <?php echo htmlspecialchars(formatDate($movieDetails['start_time'])); ?></div>
                        <div><span>End Time:</span> <?php echo htmlspecialchars(formatDate($movieDetails['end_time'])); ?></div>
                        <div><span>Hall:</span> <?php echo htmlspecialchars($movieDetails['hall_name']); ?></div>
                    </div>
                </div>
            </div>

            <div class="seats">
                <div class="left" style="max-width: 1500px;">
                    <form action="./admin/process_booking.php" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
                        <input type="hidden" name="_token" value="<?php echo htmlspecialchars($_SESSION['_token']); ?>">
                        <input type="hidden" name="hall_id" value="<?php echo htmlspecialchars($hall_id); ?>">
                        <input type="hidden" name="showtime_id" value="<?php echo htmlspecialchars($showtime_id); ?>">
                        <input type="hidden" id="total-seats" value="<?php echo htmlspecialchars($total); ?>">
                        <div class="screen">Screen</div>
                        <?php foreach ($groupedSeats as $row_number => $seatsInRow) { ?>
                            <div class="row">
                                <h1>Row <?php echo htmlspecialchars($row_number); ?></h1>
                                <div class="seats-container" style="gap: 20px;">
                                    <div class="seats-left" style="display: flex;">
                                        <?php foreach (array_slice($seatsInRow, 0, ceil(count($seatsInRow) / 2)) as $seat) { ?>
                                            <div class="seat">
                                                <label for="seat_<?php echo htmlspecialchars($seat['id']); ?>"><?php echo htmlspecialchars($seat['seat_number']); ?></label>
                                                <input type="checkbox" 
                                                       name="seat_ids[]" 
                                                       value="<?php echo htmlspecialchars($seat['id']); ?>" 
                                                       data-price="<?php echo htmlspecialchars($seat['price']); ?>"
                                                       id="seat_<?php echo htmlspecialchars($seat['id']); ?>" 
                                                       <?php if (!empty($seat['status']) && $seat['status'] != 'canceled') echo 'checked disabled'; ?>
                                                       onclick="calculateTotal()">
                                            </div>
                                        <?php } ?>
                                    </div>

                                    <div class="seats-middle"></div>

                                    <div class="seats-right" style="display: flex;">
                                        <?php foreach (array_slice($seatsInRow, ceil(count($seatsInRow) / 2)) as $seat) { ?>
                                            <div class="seat">
                                                <label for="seat_<?php echo htmlspecialchars($seat['id']); ?>"><?php echo htmlspecialchars($seat['seat_number']); ?></label>
                                                <input type="checkbox" 
                                                       name="seat_ids[]" 
                                                       value="<?php echo htmlspecialchars($seat['id']); ?>" 
                                                       data-price="<?php echo htmlspecialchars($seat['price']); ?>"
                                                       id="seat_<?php echo htmlspecialchars($seat['id']); ?>" 
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
                            <button type="submit" id="submit-button" class="btn btn-primary">Book Selected Seats</button>
                        </div>

                    </form>
                </div>

                <div class="right">
                    <div class="rdetail">
                        <div>
                            <div class="screen">Summary</div>
                            <?php foreach ($groupedSeats as $row_number => $seatsInRow) { ?>
                                <div class="row">
                                    <span class="rpc">
                                        Row <?php echo htmlspecialchars($row_number); ?>
                                          MMK<?php echo htmlspecialchars($seatsInRow[0]['price']); ?> => <?php if($row_number === 1){?>
                                        <span style="color: #555;">Normal Seat</span> 
                                    <?php } ?><?php if($row_number === 2){?>
                                       <span style="color: #555;">Normal Seat</span>
                                    <?php } ?><?php if($row_number === 3){?>
                                        <span style="color: #555;"> Couple Seat </span>
                                    <?php } ?>
                                </span>
                                    
                                </div>
                            <?php } ?>
                        </div>
                        <div class="seat-de">
                            <div>Please Select Seat</div>
                            <div class="bookseatSelectDetail">
                                <div>Movie Name: <?php echo htmlspecialchars($movieDetails['name']); ?></div>
                                <div>Seat number is: <span id="selected-seats"></span></div>
                                <div class="totalPrice">Total Price - MMK <span id="total-cost">0</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="error-message" style="color: red; display: none;"></div>
        </div>
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

        updateButtonState();
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
            alert(errorMessage.trim());
            return false; // Prevent form submission
        }

        // Allow form submission if no errors
        return true;
    }

    function updateButtonState() {
        var checkboxes = document.querySelectorAll('input[name="seat_ids[]"]:disabled');
        var totalSeats = parseInt(document.getElementById('total-seats').value);
        var button = document.getElementById('submit-button');
        console.log(totalSeats);
        if (checkboxes.length === totalSeats) {
            button.textContent = 'Full Seat Selected';
            button.disabled = true; // Disable button if all seats are selected
        } else {
            button.textContent = 'Book Selected Seats';
            button.disabled = false;
        }
    }

    function toggleDescription(event) {
    event.preventDefault(); // Prevent the default action of the link
    
    console.log('Toggle description function called'); // Debugging line

    var shortDescription = document.getElementById('shortDescription');
    var fullDescription = document.getElementById('fullDescription');
    var toggleLink = document.getElementById('toggleDescription');
    
    if (fullDescription.classList.contains('hidden')) {
        fullDescription.classList.remove('hidden');
        shortDescription.style.display = 'none';
        toggleLink.textContent = "See Less";


    } else {
        fullDescription.classList.add('hidden');
        shortDescription.style.display = 'inline';
        toggleLink.textContent = 'See More';
    }
}


    // Call updateButtonState initially and also after seat selection changes
    document.addEventListener('DOMContentLoaded', updateButtonState);
    document.querySelectorAll('input[name="seat_ids[]"]').forEach(function(checkbox) {
        checkbox.addEventListener('change', updateButtonState);
    });
    </script>

</body>
</html>

