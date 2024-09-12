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
    $nameError = $capacityError = $existError = '';

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
        $id = $_POST['id'];

        // Check if the hall with the same hall_id, seat_id, and row_id already exists
        $checkSql = "SELECT COUNT(*) FROM Halls WHERE name = :name AND id != :id";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([
            ':name' => $name,
            ':id' => $id
        ]);
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            $existError = "A hall with the same name ";
        } else {
            $sql = "UPDATE Halls SET name = :name, capacity = :capacity WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            
            $result = $stmt->execute([
                ':name' => $name,
                ':capacity' => $capacity,
                ':id' => $id
            ]);

            if ($result) {
                echo "<script>alert('Update Successful');window.location.href='halls.php'</script>";
            }
        }
    }
}

$sql = "SELECT * FROM Halls WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $_GET['id']]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<?php include('header.php'); ?>
    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-body">
                <form action="" method="post">
                  <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']?>">
                  <input type="hidden" name="id" value="<?php echo escape($result['id']) ?>">
                  <div class="form-group">
                    <label for="name">Name</label>
                    <?php if (!empty($existError)) { ?>
                      <p style="color:red"><?php echo "*".$existError; ?></p>
                  <?php } ?>
                    <p style="color:red"><?php echo empty($nameError) ? "" : "*".$nameError ?></p>
                    <input type="text" class="form-control" name="name" value="<?php echo escape($result['name']) ?>">
                  </div>
                  <div class="form-group">
                    <label for="capacity">Capacity</label>
                    <p style="color:red"><?php echo empty($capacityError) ? "" : "*".$capacityError ?></p>
                    <input type="number" class="form-control" name="capacity" value="<?php echo escape($result['capacity']) ?>">
                  </div>
                  <div class="form-group">
                    <input type="submit" class="btn btn-success" value="UPDATE">
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
