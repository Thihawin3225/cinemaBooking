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

$errors = [];
if ($_POST) {
    $movie_id = $_POST['movie_id'];
    $hall_id = $_POST['hall_id'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $id = $_POST['id'];
    $currenttime = time();
    $stime = strtotime($start_time);
    $endtime = strtotime($end_time);

    // Validate inputs
    if (empty($movie_id)) {
        $errors['movie_id'] = "Movie is required";
    }
    if (empty($hall_id)) {
        $errors['hall_id'] = "Hall is required";
    }
    if (empty($start_time)) {
        $errors['start_time'] = "Start time is required";
    }
    if (empty($end_time)) {
        $errors['end_time'] = "End time is required";
    }
    if ($stime < $currenttime) {
        $errors['start_time'] = "Start time must be in the future";
    }
    if ($endtime <= $stime) {
        $errors['end_time'] = "End time must be after start time";
    }

    if (empty($errors)) {
        // Check for conflicting showtimes
        $presql = "
            SELECT * 
            FROM showtimes 
            WHERE hall_id = :hall_id
            AND id != :id
            AND (
                (start_time < :endtime AND end_time > :stime)
            ) 
        ";
        $prestmt = $pdo->prepare($presql);
        $prestmt->execute([
            ":hall_id" => $hall_id,
            ":stime" => date('Y-m-d H:i:s', $stime),
            ":endtime" => date('Y-m-d H:i:s', $endtime),
            ":id" => $id
        ]);

        if ($prestmt->rowCount() > 0) {
            echo "<script>alert('There is a conflicting showtime during this period');window.location.href='showtimes.php';</script>";
            $errors['conflict'] = 'There is a conflicting showtime during this period';
        } else {
            // Update showtime
            $sql = "UPDATE showtimes SET movie_id = :movie_id, hall_id = :hall_id, start_time = :start_time, end_time = :end_time WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $res = $stmt->execute([
                ':movie_id' => $movie_id,
                ':hall_id' => $hall_id,
                ':start_time' => date('Y-m-d H:i:s', $stime),
                ':end_time' => date('Y-m-d H:i:s', $endtime),
                ':id' => $id
            ]);

            if ($res) {
                echo "<script>alert('Showtime updated successfully');window.location.href = 'showtimes.php';</script>";
                exit();
            } else {
                $errors['update'] = "Failed to update showtime";
            }
        }
    }
}

// Fetch showtime data
$id = $_GET['id'];
$sql = "SELECT * FROM showtimes WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$showtime = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$showtime) {
    echo "<script>alert('Showtime not found');window.location.href = 'showtimes.php';</script>";
    exit();
}

// Fetch movies and halls
$moviesStmt = $pdo->prepare('SELECT * FROM movies');
$moviesStmt->execute();
$movies = $moviesStmt->fetchAll(PDO::FETCH_ASSOC);

$hallsStmt = $pdo->prepare('SELECT * FROM halls');
$hallsStmt->execute();
$halls = $hallsStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include('header.php') ?>
<!-- Main content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Showtime</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <form action="" method="post">
                            <input type="hidden" name="_token" value="<?php echo escape($_SESSION['_token']) ?>">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($showtime['id']); ?>" />
                            
                            <div class="form-group">
                                <label for="movie_id">Movie</label>
                                <select id="movie_id" name="movie_id" class="form-control">
                                    <option value="">Select Movie</option>
                                    <?php foreach ($movies as $movie) { ?>
                                        <option value="<?php echo htmlspecialchars($movie['id']); ?>" <?php echo $movie['id'] == $showtime['movie_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($movie['name']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <?php if (isset($errors['movie_id'])) { echo '<p style="color:red">' . $errors['movie_id'] . '</p>'; } ?>
                            </div>

                            <div class="form-group">
                                <label for="hall_id">Hall</label>
                                <select id="hall_id" name="hall_id" class="form-control">
                                    <option value="">Select Hall</option>
                                    <?php foreach ($halls as $hall) { ?>
                                        <option value="<?php echo htmlspecialchars($hall['id']); ?>" <?php echo $hall['id'] == $showtime['hall_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($hall['name']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <?php if (isset($errors['hall_id'])) { echo '<p style="color:red">' . $errors['hall_id'] . '</p>'; } ?>
                            </div>

                            <div class="form-group">
                                <label for="start_time">Start Time</label>
                                <input type="datetime-local" id="start_time" name="start_time" class="form-control" value="<?php echo htmlspecialchars(date('Y-m-d\TH:i', strtotime($showtime['start_time']))); ?>">
                                <?php if (isset($errors['start_time'])) { echo '<p style="color:red">' . $errors['start_time'] . '</p>'; } ?>
                            </div>

                            <div class="form-group">
                                <label for="end_time">End Time</label>
                                <input type="datetime-local" id="end_time" name="end_time" class="form-control" value="<?php echo htmlspecialchars(date('Y-m-d\TH:i', strtotime($showtime['end_time']))); ?>">
                                <?php if (isset($errors['end_time'])) { echo '<p style="color:red">' . $errors['end_time'] . '</p>'; } ?>
                            </div>

                            <div class="form-group">
                                <input type="submit" class="btn btn-success" value="Update Showtime">
                                <a href="showtimes.php" class="btn btn-warning">Back</a>
                            </div>
                        </form>
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
<?php include('footer.html') ?>
