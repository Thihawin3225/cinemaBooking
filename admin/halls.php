<?php
    session_start();
    require '../config/config.php';
    require '../config/common.php';
    if(empty($_SESSION['user_id']) && empty($_SESSION['login_time'])){
      header('Location: login.php');
      exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        setcookie('search', $_POST['search'], time() + (86400 * 30), "/");
    } else {
        if (empty($_GET['pageno'])) {
            unset($_COOKIE['search']); 
            setcookie('search', null, -1, '/'); 
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
                    <div class="card-header">
                        <h3 class="card-title">Hall Listings</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div>
                            <a href="hall_add.php" type="button" class="btn btn-success">New Hall</a>
                        </div>
                        <br>
                        
                        <br>
                        <?php
                          if(!empty($_GET['pageno'])){
                            $pageno = $_GET['pageno'];
                          }else{
                            $pageno = 1;
                          }
                          $numberOfrec = 10;
                          $offset = ($pageno-1) * $numberOfrec;

                          if (empty($_POST['search']) && empty($_COOKIE['search'])) {
                              $sql = "SELECT * FROM Halls";
                              $stmt = $pdo->prepare($sql);
                              $stmt->execute();
                              $rawhalls = $stmt->fetchAll();
                          } else {
                              $searchKey = isset($_POST['search']) ? $_POST['search'] : $_COOKIE['search'];
                              $sql = "SELECT * FROM Halls WHERE name LIKE :searchKey";
                              $stmt = $pdo->prepare($sql);
                              $stmt->execute([':searchKey' => '%' . $searchKey . '%']);
                              $rawhalls = $stmt->fetchAll();
                          }

                          $totalPages = ceil(count($rawhalls) / $numberOfrec);

                          $sql = "SELECT * FROM Halls LIMIT $offset,$numberOfrec";
                          if (!empty($searchKey)) {
                              $sql = "SELECT * FROM Halls WHERE name LIKE :searchKey LIMIT $offset,$numberOfrec";
                              $stmt = $pdo->prepare($sql);
                              $stmt->execute([':searchKey' => '%' . $searchKey . '%']);
                          } else {
                              $stmt = $pdo->prepare($sql);
                              $stmt->execute();
                          }
                          $halls = $stmt->fetchAll();
                        ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>Name</th>
                                    <th>Capacity</th>
                                    <th style="width: 40px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if($halls){
                                  $i = 1;
                                  foreach($halls as $value){ 
                                    ?>
                                  
                                  <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo escape($value['name']);?></td>
                                    <td><?php echo escape($value['capacity']); ?></td>
                                   <td>
                                        <div class="btn-group">
                                          <div class="container">
                                            <a href="hall_edit.php?id=<?php echo $value['id']?>" type="button" class="btn btn-warning">Edit</a>
                                          </div>
                                          <div class="container">
                                            <a href="hall_delete.php?id=<?php echo $value['id']?>"
                                              onclick="return confirm('Are you sure you want to delete this hall?')"
                                              type="button" class="btn btn-danger">Delete</a>
                                          </div>
                                        </div>
                                      </td>
                                    </tr>
                                <?php
                                $i++;
                                  }
                                }
                                ?>
                            </tbody>
                        </table><br>
                        <nav aria-label="Page navigation example" style="float:right">
                            <ul class="pagination">
                                <li class="page-item"><a class="page-link" href="?pageno=1">First</a></li>
                                <li class="page-item <?php if($pageno <= 1){echo 'disabled';}else{echo "";} ?>">
                                    <a class="page-link" href="<?php if($pageno <= 1){ echo '#';} else { echo '?pageno='.($pageno-1);} ?>">Previous</a>
                                </li>
                                <li class="page-item"><a class="page-link" href="#"><?php echo $pageno; ?></a></li>
                                <li class="page-item <?php if($pageno >= $totalPages){echo 'disabled';}else{echo "";} ?>">
                                    <a class="page-link" href="<?php if($pageno >= $totalPages){ echo $totalPages;} else { echo '?pageno='.($pageno+1);} ?>">Next</a>
                                </li>
                                <li class="page-item"><a class="page-link" href="?pageno=<?php echo $totalPages ?>" >Last</a></li>
                            </ul>
                        </nav>
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
<?php include('footer.html')?>
