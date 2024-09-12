<?php
session_start();
require '../config/config.php';
require '../config/common.php';

if(empty($_SESSION['user_id']) && empty($_SESSION['login_time'])){
    header('Location: login.php');
    exit();
}
if($_SESSION['role'] !=1){
  echo "<script>alert('You are not admin');window.location.href='login.php';</script>";
}

if($_POST){
    $name = $_POST['name'];
    $description = $_POST['description'];
    $releaseDate = $_POST['release_date'];
    $duration = $_POST['duration'];
    $rating = $_POST['rating'];
    $genre = $_POST['genre'];
    $image = $_FILES['image'];

    $errors = [];

    if(empty($name)) {
        $errors['name'] = "Name is required";
    }
    if(empty($description)) {
        $errors['description'] = "Description is required";
    }
    if(empty($releaseDate)) {
        $errors['release_date'] = "Release Date is required";
    }
    if(empty($duration) || !is_numeric($duration)) {
        $errors['duration'] = "Duration is required and must be a number";
    }
    if(empty($rating) || !is_numeric($rating)) {
        $errors['rating'] = "Rating is required and must be a number";
    }
    if(empty($genre)) {
        $errors['genre'] = "Genre is required";
    }
    if(empty($image['name'])) {
        $errors['image'] = "Image is required";
    } else {
        $allowedTypes = ['jpg', 'jpeg', 'png'];
        $imageType = pathinfo($image['name'], PATHINFO_EXTENSION);

        if(!in_array($imageType, $allowedTypes)) {
            $errors['image'] = "Image must be a JPG, JPEG, or PNG file";
        }
    }

    if(empty($errors)){
        $imagePath = 'uploads/' . basename($image['name']);
        if(move_uploaded_file($image['tmp_name'], $imagePath)){
            $sql = "INSERT INTO movies (name, description, release_date, duration, rating, genre, image) 
                    VALUES (:name, :description, :release_date, :duration, :rating, :genre, :image)";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                ':name' => $name,
                ':description' => $description,
                ':release_date' => $releaseDate,
                ':duration' => $duration,
                ':rating' => $rating,
                ':genre' => $genre,
                ':image' => $imagePath,
            ]);

            if($result){
                echo "<script>alert('Movie added successfully');window.location.href = 'index.php';</script>";
            } else {
                echo "<script>alert('Error adding movie');</script>";
            }
        } else {
            $errors['image'] = "Failed to upload image";
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
            <form action="movie_add.php" method="post" enctype="multipart/form-data">
              <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">
              <div class="form-group">
                <label for="">Name</label>
                <p style="color:red"><?php echo empty($errors['name']) ? '' : '*'.$errors['name']; ?></p>
                <input type="text" class="form-control" name="name" value="">
              </div>
              <div class="form-group">
                <label for="">Description</label>
                <p style="color:red"><?php echo empty($errors['description']) ? '' : '*'.$errors['description']; ?></p>
                <textarea class="form-control" name="description" rows="8" cols="80"></textarea>
              </div>
              <div class="form-group">
                <label for="">Release Date</label>
                <p style="color:red"><?php echo empty($errors['release_date']) ? '' : '*'.$errors['release_date']; ?></p>
                <input type="date" class="form-control" name="release_date" value="">
              </div>
              <div class="form-group">
                <label for="">Duration (in minutes)</label>
                <p style="color:red"><?php echo empty($errors['duration']) ? '' : '*'.$errors['duration']; ?></p>
                <input type="number" class="form-control" name="duration" value="">
              </div>
              <div class="form-group">
                <label for="">Rating</label>
                <p style="color:red"><?php echo empty($errors['rating']) ? '' : '*'.$errors['rating']; ?></p>
                <input type="number" step="0.1" class="form-control" name="rating" value="">
              </div>
              <div class="form-group">
                <label for="">Genre</label>
                <p style="color:red"><?php echo empty($errors['genre']) ? '' : '*'.$errors['genre']; ?></p>
                <input type="text" class="form-control" name="genre" value="">
              </div>
              <div class="form-group">
                <label for="">Image</label>
                <p style="color:red"><?php echo empty($errors['image']) ? '' : '*'.$errors['image']; ?></p>
                <input type="file" class="form-control" name="image">
              </div>
              <div class="form-group">
                <input type="submit" class="btn btn-success" value="Submit">
                <a href="movies.php" class="btn btn-warning">Back</a>
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
