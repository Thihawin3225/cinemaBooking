<?php
session_start();
require '../config/config.php';
require '../config/common.php';

if (!empty($_POST)) {
    if (empty($_POST['name']) || empty($_POST['capacity'])) {
        if (empty($_POST['name'])) {
            $nameError = "Name is required";
        }
        if (empty($_POST['capacity'])) {
            $capacityError = "Capacity is required";
        }
    } else {
        $name = $_POST['name'];
        $capacity = $_POST['capacity'];

        // Check if the hall already exists
        $checkSql = "SELECT * FROM Halls WHERE name = :name";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([':name' => $name]);
        $existingHall = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($existingHall) {
            $nameError = "Hall with this name already exists";
        } else {
            // Insert new hall
            $sql = "INSERT INTO Halls (name, capacity) VALUES (:name, :capacity)";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute(
                [
                    ':name' => $name,
                    ':capacity' => $capacity
                ]
            );
            if ($result) {
                echo "<script>alert('Hall added');window.location.href = 'halls.php';</script>";
            }
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
                <form action="hall_add.php" method="post">
                   <input name="_token" type="hidden" value="<?php echo $_SESSION['_token']; ?>">
                  <div class="form-group">
                    <label for="name">Name</label>
                    <p style="color:red"><?php echo empty($nameError) ? '' : '*' . $nameError; ?></p>
                    <input type="text" class="form-control" name="name" value="">
                  </div>
                  <div class="form-group">
                    <label for="capacity">Capacity</label>
                    <p style="color:red"><?php echo empty($capacityError) ? '' : '*' . $capacityError; ?></p>
                    <input type="number" class="form-control" name="capacity" value="">
                  </div>
                  <div class="form-group">
                    <input type="submit" class="btn btn-success" value="SUBMIT">
                    <a href="halls.php" class="btn btn-warning">Back</a>
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
