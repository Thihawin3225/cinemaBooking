<?php
session_start();
require './config/config.php';
require './config/common.php';

if(!empty($_POST)){
    $email = $_POST['email'];
    $password = $_POST['password'];
    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':email',$email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if($user){
        if(password_verify($password,$user['password'])){
            $_SESSION['userId'] = $user['id'];
            $_SESSION['userName'] = $user['name'];
            $_SESSION['loginTime'] = time();
            header('Location:index.php');
        }
    }
    echo "<script>alert('Incorrect credentials');window.location.href = 'login.php';</script>";
}

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Cinema Booking | Log in</title>
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
</head>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: sans-serif;
}
html{
    font-family: sans-serif;
}
body{
    font-family: "Poppins", Arial, sans-serif;
    font-size: 16px;
    line-height: 1.8;
    font-weight: normal;
    background: #fafafa;
    color: #666666;
}
ul{
  margin-bottom: 0;
}
.nav-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    background-color: #002970; /* Set background color */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Add box shadow */
    position: sticky;
    top: 0;
    z-index: 1;
}

ul li a{
    text-decoration: none;
    color: #fff;
    font-size: 20px;
    border-radius: 10px;
    padding: 10px 20px;
}
ul li a:hover{
    background-color: #E0F5FD;
}
a {
    color: #007bff;
    text-decoration: none;
    background-color: transparent; }
    a:hover {
      color: #0056b3;
       }
  
  a:not([href]):not([tabindex]) {
    color: inherit;
    text-decoration: none; }
    a:not([href]):not([tabindex]):hover, a:not([href]):not([tabindex]):focus {
      color: inherit;
      text-decoration: none; }
    a:not([href]):not([tabindex]):focus {
      outline: 0; }
.nav-bar ul{
    display: flex;
    align-items: center;
    gap: 40px;
    list-style: none;
}
.cinema-heading {
    width: 100%; /* Make the image responsive to the container width */
    max-width: 150px; /* Set a maximum width for the image */
    height: auto; /* Maintain the aspect ratio of the image */
    border-radius: 15px; /* Rounded corners for a smoother look */
    display: block; /* Ensure image is a block element for alignment */
}

  </style>
<body>
  <nav class="nav-bar">
  <img src="./images/Screenshot_2024-09-08_163828-removebg-preview.png" class="cinema-heading" alt="">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="move.php">Movies</a></li>
                <li><a href="./contactus/index.php">Contact us</a></li>
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
        <div class="hold-transition login-page" style="height: 80vh;">
<div class="login-box">
  <div class="login-logo">
    <a href="index2.html"><b>Cinema Booking </b>Login</a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Sign in to start your session</p>

      <form action="login.php" method="post">
        <div class="input-group mb-3">
            <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']?>">
          <input type="email" name="email" class="form-control" placeholder="Email">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            <a href="register.php" class="btn btn-primary btn-block">Register</a>
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
  </div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="./admin/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="./admin/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="./admin/dist/js/adminlte.min.js"></script>

</body>
</html>