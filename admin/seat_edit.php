<?php
session_start();
require '../config/config.php';
require '../config/common.php';

// Check if the user is logged in
if (empty($_SESSION['user_id']) || empty($_SESSION['login_time'])) {
    header('Location: login.php');
    exit();
}

// Check if seat ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: seat_manage.php');
    exit();
}

$seat_id = $_GET['id'];

// Fetch seat details
$sql = "SELECT * FROM seats WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $seat_id]);
$seat = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if seat exists
if (!$seat) {
    header('Location: seat_manage.php');
    exit();
}

// Fetch halls for the dropdown
$hallStmt = $pdo->prepare("SELECT * FROM halls");
$hallStmt->execute();
$halls = $hallStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
$hallIdErr = $seatNumberErr = $rowNumberErr = $existErr = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hall_id = $_POST['hall_id'];
    $seat_number = $_POST['seat_number'];
    $row_number = $_POST['row_number'];

    // Form validation
    if (empty($hall_id)) {
        $hallIdErr = 'Hall is required';
    }
    if (empty($seat_number)) {
        $seatNumberErr = 'Seat number is required';
    }
    if (empty($row_number)) {
        $rowNumberErr = 'Row number is required';
    }

    if (empty($hallIdErr) && empty($seatNumberErr) && empty($rowNumberErr)) {
        // Check if the seat already exists
        $checkSql = "SELECT COUNT(*) FROM seats WHERE hall_id = :hall_id AND seat_number = :seat_number AND row_number = :row_number AND id != :id";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([
            ':hall_id' => $hall_id,
            ':seat_number' => $seat_number,
            ':row_number' => $row_number,
            ':id' => $seat_id
        ]);
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            $existErr = 'This seat already exists in the selected hall and row.';
        } else {
            // Update seat details
            $updateSql = "UPDATE seats SET hall_id = :hall_id, seat_number = :seat_number, row_number = :row_number WHERE id = :id";
            $updateStmt = $pdo->prepare($updateSql);
            $result = $updateStmt->execute([
                ':hall_id' => $hall_id,
                ':seat_number' => $seat_number,
                ':row_number' => $row_number,
                ':id' => $seat_id
            ]);

            if ($result) {
                echo "<script>alert('Update successful');window.location.href = 'seat_manage.php';</script>";
            } else {
                echo "<script>alert('Failed to update seat');</script>";
            }
        }
    }
}

include('header.php');
?>

<!-- Main content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="seat_edit.php?id=<?php echo $seat_id; ?>" method="post">
                            <input type="hidden" name="_token" value="<?php echo $_SESSION['_token'] ?>">
                            <div class="form-group">
                                <label for="hall_id">Hall</label>
                                <p style="color:red"><?php echo empty($hallIdErr) ? "" : "*" . $hallIdErr ?></p>
                                <select name="hall_id" id="hall_id" class="form-control" required>
                                    <?php foreach ($halls as $hall): ?>
                                        <option value="<?php echo $hall['id']; ?>" <?php echo $hall['id'] == $seat['hall_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($hall['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="seat_number">Seat Number</label>
                                <p style="color:red"><?php echo empty($seatNumberErr) ? "" : "*" . $seatNumberErr ?></p>
                                <input type="number" id="seat_number" name="seat_number" class="form-control" value="<?php echo htmlspecialchars($seat['seat_number']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="row_number">Row Number</label>
                                <p style="color:red"><?php echo empty($rowNumberErr) ? "" : "*" . $rowNumberErr ?></p>
                                <input type="number" id="row_number" name="row_number" class="form-control" value="<?php echo htmlspecialchars($seat['row_number']); ?>" required>
                            </div>
                            <?php if (!empty($existErr)) { ?>
                                <p class="text-danger"><?php echo $existErr; ?></p>
                            <?php } ?>
                            <div class="form-group">
                                <input type="submit" class="btn btn-success" value="Update Seat">
                                <a href="seat_manage.php" class="btn btn-warning">Back</a>
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
<?php include('footer.html'); ?>
