<?php
session_start();
require '../config/config.php';
require '../config/common.php';

if (empty($_SESSION['user_id']) || empty($_SESSION['login_time'])) {
    header('Location: login.php');
    exit();
}
if($_SESSION['role'] !=1){
    echo "<script>alert('You are not admin');window.location.href='login.php';</script>";
}

if ($_POST) {
    $movie_id = $_POST['movie_id'];
    $hall_id = $_POST['hall_id'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $currenttime = time();
    $stime = strtotime($start_time);
    $endtime = strtotime($end_time);

    // Initialize error variables
    $movieError = $hallError = $startTimeError = $endTimeError = '';

    // Validate inputs
    if (empty($movie_id)) {
        $movieError = "Movie is required";
    }
    if (empty($hall_id)) {
        $hallError = "Hall is required";
    }
    if (empty($start_time)) {
        $startTimeError = "Start time is required";
    }
    if (empty($end_time)) {
        $endTimeError = "End time is required";
    }
    if ($stime < $currenttime) {
        $startTimeError = "Start time is less than the current date and time";
    }
    if ($endtime <= $stime) {
        $endTimeError = "End time is less than or equal to start time";
    }

    if (empty($movieError) && empty($hallError) && empty($startTimeError) && empty($endTimeError)) {
        // Check for overlapping showtimes
        $presql = "
            SELECT * 
            FROM showtimes 
            WHERE hall_id = :hall_id
            AND (
                (start_time < :endtime AND end_time > :stime)
            )
        ";
        $prestmt = $pdo->prepare($presql);
        $prestmt->execute([
            ":hall_id" => $hall_id,
            ":stime" => date('Y-m-d H:i:s', $stime),
            ":endtime" => date('Y-m-d H:i:s', $endtime)
        ]);

        $preres = $prestmt->fetchAll();
        if ($preres) {
            echo "<script>alert('There is a conflicting showtime during this period');window.location.href='showtime_add.php';</script>";
            exit();
        }

        // Insert new showtime
        $sql = "INSERT INTO showtimes (movie_id, hall_id, start_time, end_time) VALUES (:movie_id, :hall_id, :start_time, :end_time)";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            ':movie_id' => $movie_id,
            ':hall_id' => $hall_id,
            ':start_time' => $start_time,
            ':end_time' => $end_time
        ]);

        if ($result) {
            echo "<script>alert('Showtime added successfully');window.location.href = 'showtimes.php';</script>";
            exit();
        } else {
            echo "<script>alert('Failed to add showtime');window.location.href='showtime_add.php';</script>";
            exit();
        }
    }
}
?>
<?php include('header.php'); ?>
<!-- Main content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="showtime_add.php" method="post">
                            <input type="hidden" name="_token" value="<?php echo $_SESSION['_token'] ?>">
                            <div class="form-group">
                                <label for="movie_id">Movie</label>
                                <p style="color:red"><?php echo empty($movieError) ? '' : '* ' . $movieError; ?></p>
                                <select class="form-control" name="movie_id">
                                    <option value="">Select Movie</option>
                                    <?php
                                    $sql = "SELECT * FROM movies";
                                    $stmt = $pdo->prepare($sql);
                                    $stmt->execute();
                                    $movies = $stmt->fetchAll();
                                    foreach ($movies as $movie) {
                                        echo "<option value='" . htmlspecialchars($movie['id']) . "'>" . htmlspecialchars($movie['name']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="hall_id">Hall</label>
                                <p style="color:red"><?php echo empty($hallError) ? '' : '* ' . $hallError; ?></p>
                                <select class="form-control" name="hall_id">
                                    <option value="">Select Hall</option>
                                    <?php
                                    $sql = "SELECT * FROM halls";
                                    $stmt = $pdo->prepare($sql);
                                    $stmt->execute();
                                    $halls = $stmt->fetchAll();
                                    foreach ($halls as $hall) {
                                        echo "<option value='" . htmlspecialchars($hall['id']) . "'>" . htmlspecialchars($hall['name']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="start_time">Start Time</label>
                                <p style="color:red"><?php echo empty($startTimeError) ? '' : '* ' . $startTimeError; ?></p>
                                <input type="datetime-local" class="form-control" name="start_time" value="<?php echo htmlspecialchars(date('Y-m-d\TH:i', strtotime($start_time))); ?>">
                            </div>
                            <div class="form-group">
                                <label for="end_time">End Time</label>
                                <p style="color:red"><?php echo empty($endTimeError) ? '' : '* ' . $endTimeError; ?></p>
                                <input type="datetime-local" class="form-control" name="end_time" value="<?php echo htmlspecialchars(date('Y-m-d\TH:i', strtotime($end_time))); ?>">
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-success" value="Submit">
                                <a href="showtimes.php" class="btn btn-warning">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content -->
<?php include('footer.html') ?>
