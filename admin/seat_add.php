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

$hall_id = $seat_number = $row_number = '';
$hallErr = $seatErr = $rowErr = $existErr = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate hall ID
    if (empty($_POST['hall_id'])) {
        $hallErr = 'Hall is required';
    } else {
        $hall_id = $_POST['hall_id'];
    }

    // Validate seat number
    if (empty($_POST['seat_number'])) {
        $seatErr = 'Seat number is required';
    } else {
        $seat_number = $_POST['seat_number'];
    }

    // Validate row number
    if (empty($_POST['row_number'])) {
        $rowErr = 'Row number is required';
    } else {
        $row_number = $_POST['row_number'];
    }

    // If no validation errors, check for existing seat and insert into the database
    if (empty($hallErr) && empty($seatErr) && empty($rowErr)) {
        // Check if the seat already exists
        $checkSql = "SELECT COUNT(*) FROM seats WHERE hall_id = :hall_id AND seat_number = :seat_number AND row_number = :row_number";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([
            ':hall_id' => $hall_id,
            ':seat_number' => $seat_number,
            ':row_number' => $row_number
        ]);
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            $existErr = 'This seat already exists in the selected hall and row.';
        } else {
            // Insert the seat into seats table
            $stmt = $pdo->prepare("INSERT INTO seats (hall_id, seat_number, row_number) VALUES (:hall_id, :seat_number, :row_number)");
            $result = $stmt->execute([
                ':hall_id' => $hall_id,
                ':seat_number' => $seat_number,
                ':row_number' => $row_number
            ]);

            if ($result) {
                echo "<script>alert('New seat added');window.location.href='seat_manage.php';</script>";
            } else {
                echo "<script>alert('Failed to add seat');</script>";
            }
        }
    }
}

// Fetch hall IDs for dropdown
$hallsStmt = $pdo->prepare("SELECT id, name FROM halls");
$hallsStmt->execute();
$halls = $hallsStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch row numbers and prices for dropdown
$rowsStmt = $pdo->prepare("SELECT row_number, price FROM rowandprice");
$rowsStmt->execute();
$rows = $rowsStmt->fetchAll(PDO::FETCH_ASSOC);

include('header.php');
?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Add Seat</h5>
                        <form action="" method="post">
                            <input type="hidden" name="_token" value="<?php echo $_SESSION['_token'] ?>">
                            <div class="form-group">
                                <label for="hall_id">Hall</label>
                                <select class="form-control" name="hall_id">
                                    <option value="">Select Hall</option>
                                    <?php foreach ($halls as $hall) { ?>
                                        <option value="<?php echo htmlspecialchars($hall['id']); ?>" <?php echo ($hall['id'] == $hall_id) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($hall['name']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <p class="text-danger"><?php echo $hallErr; ?></p>
                            </div>
                            <div class="form-group">
                                <label for="seat_number">Seat Number</label>
                                <input type="number" class="form-control" name="seat_number" value="<?php echo htmlspecialchars($seat_number); ?>">
                                <p class="text-danger"><?php echo $seatErr; ?></p>
                            </div>
                            <div class="form-group">
                                <label for="row_number">Row Number</label>
                                <select class="form-control" name="row_number">
                                    <option value="">Select Row</option>
                                    <?php foreach ($rows as $row) { ?>
                                        <option value="<?php echo htmlspecialchars($row['row_number']); ?>" <?php echo ($row['row_number'] == $row_number) ? 'selected' : ''; ?>>
                                            Row <?php echo htmlspecialchars($row['row_number']); ?> - MMK<?php echo htmlspecialchars($row['price']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <p class="text-danger"><?php echo $rowErr; ?></p>
                            </div>
                            <?php if (!empty($existErr)) { ?>
                                <p class="text-danger"><?php echo $existErr; ?></p>
                            <?php } ?>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Add</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('footer.html'); ?>
