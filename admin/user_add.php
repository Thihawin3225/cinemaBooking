<?php
  session_start();
  require '../config/config.php';
  require '../config/common.php';
  if(empty($_SESSION['user_id']) && empty($_SESSION['login_time'])){
    header('Location: login.php');
  }
  if($_POST){
    if(empty($_POST['name']) || empty($_POST['email']) 
    || empty($_POST['phone']) || empty($_POST['address']) ||
    empty($_POST['password']) || strlen($_POST['password']) < 4
)
{
    if(empty($_POST['name'])){
        $nameError = 'name is required';
    } 
    if(empty($_POST['email'])){
        $emailError = 'email is required';
    }
    if(empty($_POST['phone'])){
        $phoneError = 'phone number is required';
    }
    if(empty($_POST['address'])){
        $addressError = 'address is required';
    }
    if(empty($_POST['password'])){
      $passwordError = "Password is required";
    }else if(strlen($_POST['password']) < 4){
        $passwordError = "password must have 4 character";
    }
}else{
  $name = $_POST['name'];
  $email = $_POST['email'];
  $hashpassword = password_hash($_POST['password'],PASSWORD_DEFAULT);
  $phone =  $_POST['phone'];
  $address = $_POST['address'];
  if(empty($_POST['role'])){
    $role = 0;
  }else{
    $role = $_POST['role'];
  }
  $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
  $stmt->bindValue(':email',$email);
  $stmt->execute();
  $res = $stmt->fetch(PDO::FETCH_ASSOC);
  if($res){
    echo "<script>alert('Dublicate Email');window.location.href = 'user_add.php';</script>";
  }else{
  $sql = "INSERT INTO users(name,email,password,phone,address,role) VALUES (:name,:email,:password,:phone,:address,:role)";
  $stmt = $pdo->prepare($sql);
  $result = $stmt->execute([
    ':name'=> $name,
    ':email'=>$email,
    ':password'=>$hashpassword,
    ':phone'=>$phone,
    ':address'=>$address,
    ':role'=>$role
  ]);
  if($result){
    echo "<script>alert('user add is sucessful');window.location.href = 'user_list.php';</script>";
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
                <form class="" action="user_add.php" method="post" enctype="multipart/form-data">
                  <input name="_token" type="hidden" value="<?php echo $_SESSION['_token']; ?>">

                  <div class="form-group">
                    <label for="">Name</label><p style="color:red"><?php echo empty($nameError) ? '' : '*'.$nameError; ?></p>
                    <input type="text" class="form-control" name="name" value="" >
                  </div>
                  <div class="form-group">
                    <label for="">Email</label><p style="color:red"><?php echo empty($emailError) ? '' : '*'.$emailError; ?></p>
                    <input type="email" class="form-control" name="email" value="">
                  </div>
                  <div class="form-group">
                    <label for="">Phone</label><p style="color:red"><?php echo empty($phoneError) ? '' : '*'.$phoneError; ?></p>
                    <input type="text" class="form-control" name="phone" value="" >
                  </div>
                  <div class="form-group">
                    <label for="">Address</label><p style="color:red"><?php echo empty($addressError) ? '' : '*'.$addressError; ?></p>
                    <input type="text" class="form-control" name="address" value="" >
                  </div>
                  <div class="form-group">
                    <label for="">Password</label><p style="color:red"><?php echo empty($passwordError) ? '' : '*'.$passwordError; ?></p>
                    <input type="password" name="password" class="form-control">
                  </div>
                  <div class="form-group">
                    <label for="vehicle3"> Admin</label><br>
                    <input type="checkbox" name="role" value="1">
                  </div>
                  <div class="form-group">
                    <input type="submit" class="btn btn-success" name="" value="SUBMIT">
                    <a href="user_list.php" class="btn btn-warning">Back</a>
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
  <?php include('footer.html')?>