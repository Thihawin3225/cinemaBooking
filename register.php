<?php
  session_start();
  require './config/config.php';
  require './config/common.php';
  
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
    echo "<script>alert('Dublicate Email');window.location.href = 'register.php';</script>";
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
    echo "<script>alert('Register sucessful');window.location.href = 'login.php';</script>";
  }
  }
}
  }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>AP Shopping | Log in</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="./admin/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="./admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="./admin/dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
  <style>
    .nav-bar {
    display: flex;
    justify-content: space-around;
    align-items: center;
    padding: 20px;
    background-color: #FFFFFF; /* Set background color */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Add box shadow */
    position: sticky;
    top: 0;
    z-index: 1;
}

ul li a{
    text-decoration: none;
    color: #333;
    font-size: 20px;
    border-radius: 10px;
    padding: 10px 20px;
}
ul li a:hover{
    background-color: #E0F5FD;
}
.nav-bar ul{
    display: flex;
    align-items: center;
    gap: 40px;
    list-style: none;
}
  </style>
<body>
<nav class="nav-bar">
            <h1>Cinema Booking</h1>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="move.php">Movies</a></li>
                <li><a href="#">Contact us</a></li>
            </ul>
            <ul>
                <?php if (!empty($_SESSION['userName'])) { ?>
                    <li><a href="./admin/booking_success.php"><?php echo htmlspecialchars($_SESSION['userName']); ?></a></li>
                    <li><a href="ulogout.php">Logout</a></li>
                <?php } else { ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php } ?>
            </ul>
        </nav>
        <div class="hold-transition login-page" style="background-color: #F8F8F8;">
<div class="login-box">
  <div class="login-logo">
    <a href="index2.html"><b>Cinema Booking </b>Register</a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Sign in to start your session</p>

      <form action="" method="post">
      <p style="color:red"><?php echo empty($nameError) ? '' : '*'.$nameError; ?></p>
      <div class="input-group mb-3">
          <input type="text" name="name" class="form-control" placeholder="Name">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <p style="color:red"><?php echo empty($emailError) ? '' : '*'.$emailError; ?></p>
        <div class="input-group mb-3">
        <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']?>">
          <input type="email" name="email" class="form-control" placeholder="Email">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <p style="color:red"><?php echo empty($passwordError) ? '' : '*'.$passwordError; ?></p>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <p style="color:red"><?php echo empty($addressError) ? '' : '*'.$addressError; ?></p>
        <div class="input-group mb-3">
          <input type="text" name="address" class="form-control" placeholder="address">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <p style="color:red"><?php echo empty($phoneError) ? '' : '*'.$phoneError; ?></p>
        <div class="input-group mb-3">
          <input type="text" name="phone" class="form-control" placeholder="phone">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        
        <div class="row">
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Register</button>
            <a href="register.php" class="btn btn-primary btn-block">Login</a>
          </div>
         
          <!-- /.col -->
        </div>
      </form>

      <!-- <p class="mb-0">
        <a href="register.html" class="text-center">Register a new membership</a>
      </p> -->
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->
</div>
<!-- jQuery -->
<script src="./admin/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="./admin/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="./admin/dist/js/adminlte.min.js"></script>

</body>
</html>