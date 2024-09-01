<?php
session_start();
require '../config/config.php';
require '../config/common.php';

if(empty($_SESSION['user_id']) || empty($_SESSION['login_time'])){
 header('Location: login.php');
}

if($_POST){
  if(empty($_POST['name']) || empty($_POST['email']) || empty($_POST['phone'])
   || empty($_POST['address'])){
    if(empty($_POST['name'])){
      $nameErr = 'name is required';
    }
    if(empty($_POST['email'])){
      $emailErr = 'email is required';
    }
    if(empty($_POST['phone'])){
      $phoneErr = 'phone is required';
    }

    if(strlen($_POST['password']) < 4){
      $passwordErr = 'password must have 4 character';
    }

    if(empty($_POST['address'])){
      $addressErr = 'address is required';
    }
  }else if($_POST['password'] && strlen( $_POST['password']) < 4){
    $passwordErr = 'password must have 4 character';
  }else{
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    if(!empty($_POST['role'])){
       $role = $_POST['role'];
    }else{
      $role = 0;
    }
    $id = $_POST['id'];
    $sql = "SELECT * FROM users WHERE id != :id and email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':id'=>$id,
      ':email'=> $email
    ]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    if($_POST['password'] == null){
      if(empty($res)){
        $stmt = $pdo->prepare('UPDATE users SET name=:name , email=:email, phone = :phone , address = :address , role = :role where id = :id');
        $result = $stmt->execute([
          ':name'=>$name,
          ':email'=>$email,
          ':phone'=>$phone,
          ':address'=>$address,
          ':role'=>$role,
          ':id'=>$id
        ]);
      } 
      else{
        echo "<script>alert('email duplicated!');window.location.href = 'user_edit.php?id=$id';</script>";
      }
    }else{
      if(empty($res)){
        $password = password_hash($_POST['password'],PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('UPDATE users SET name=:name , email=:email,password = :password ,phone = :phone , address = :address , role = :role where id = :id');
        $result = $stmt->execute([
          ':name'=>$name,
          ':email'=>$email,
          'password'=> $password,
          ':phone'=>$phone,
          ':address'=>$address,
          ':role'=>$role,
          ':id'=>$id
        ]);
      }else{
        echo "<script>alert('email duplicated!');window.location.href = 'user_edit.php?id=$id';</script>";
      }
    }
    if($result){
      echo "<script>alert('update sucessful');window.location.href = 'user_list.php';</script>";
    }
    
  }

}

?>
<?php include('header.php'); ?>
<?php 
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id=".$_GET['id']);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

?>
    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-body">
                <form class="" action="" method="post" enctype="multipart/form-data">
                  <input name="_token" type="hidden" value="<?php echo $_SESSION['_token'] ?>">

                  <div class="form-group">
                    <input type="hidden" name="id" value="<?php echo escape($result['id'])?>">
                    <label for="">Name</label>
                    <p style="color:red"><?php echo empty($nameErr) ? "" : "*".$nameErr ?></p>
                    <input type="text" class="form-control" name="name" value="<?php echo escape($result['name'])?>">
                  </div>
                  <div class="form-group">
                    <label for="">Email</label>
                    <p style="color:red"><?php echo empty($emailErr) ? "" : "*".$emailErr ?></p>
                    <input type="email" class="form-control" name="email" value="<?php echo escape($result['email'])?>">
                  </div>
                  <div class="form-group">
                    <label for="">Phone</label>
                    <p style="color:red"><?php echo empty($phoneErr) ? "" : "*".$phoneErr ?></p>
                    <input type="text" class="form-control" name="phone" value="<?php echo escape($result['phone'])?>">
                  </div>
                  <div class="form-group">
                    <label for="">Address</label>
                    <p style="color:red"><?php echo empty($addressErr) ? "" : "*".$addressErr ?></p>
                    <input type="text" class="form-control" name="address" value="<?php echo escape($result['address'])?>">
                  </div>
                  <div class="form-group">
                    <label for="">Password</label>
                    <p style="color:red"><?php echo empty($passwordErr) ? "" : "*".$passwordErr ?></p>
                    <span style="font-size:10px">The user already has a password</span>
                    <input type="password" name="password" class="form-control">
                  </div>
                  <div class="form-group">
                    <label for="">Role</label>
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