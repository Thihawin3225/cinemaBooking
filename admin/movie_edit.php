<?php
session_start();
require '../config/config.php';
require '../config/common.php';

if (empty($_SESSION['user_id']) && empty($_SESSION['login_time'])) {
    header('Location: login.php');
    exit();
}

if ($_POST) {
    if (empty($_POST['name']) || empty($_POST['description']) || empty($_POST['genre']) || 
        empty($_POST['release_date']) || empty($_POST['duration']) || !is_numeric($_POST['duration']) || 
        empty($_POST['rating']) || !is_numeric($_POST['rating'])) {
        
        if (empty($_POST['name'])) {
            $nameError = "Name is required";
        }
        if (empty($_POST['description'])) {
            $descError = "Description is required";
        }
        if (empty($_POST['genre'])) {
            $genreError = "Genre is required";
        }
        if (empty($_POST['release_date'])) {
            $releaseDateError = "Release Date is required";
        }
        if (empty($_POST['duration'])) {
            $durationError = "Duration is required";
        } elseif (!is_numeric($_POST['duration'])) {
            $durationError = "Duration must be a number";
        }
        if (empty($_POST['rating'])) {
            $ratingError = "Rating is required";
        } elseif (!is_numeric($_POST['rating'])) {
            $ratingError = "Rating must be a number";
        }
    } else {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $genre = $_POST['genre'];
        $releaseDate = $_POST['release_date'];
        $duration = $_POST['duration'];
        $rating = $_POST['rating'];
        $id = $_POST['id'];

        if (!empty($_FILES['image']['name'])) {
            $file = 'uploads/' . $_FILES['image']['name'];
            $imageType = pathinfo($file, PATHINFO_EXTENSION);

            if ($imageType != 'jpg' && $imageType != 'jpeg' && $imageType != 'png') {
                echo "<script>alert('Image should be png or jpg or jpeg');</script>";
            } else {
                move_uploaded_file($_FILES['image']['tmp_name'], $file);
                $sql = "UPDATE movies SET name = :name, description = :description, genre = :genre, release_date = :release_date, duration = :duration, rating = :rating, image = :image WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $res = $stmt->execute([
                    ':name' => $name,
                    ':description' => $description,
                    ':genre' => $genre,
                    ':release_date' => $releaseDate,
                    ':duration' => $duration,
                    ':rating' => $rating,
                    ':image' => $file,
                    ':id' => $id
                ]);

                if ($res) {
                    echo "<script>alert('Movie updated successfully'); window.location.href = 'index.php';</script>";
                }
            }
        } else {
            $sql = "UPDATE movies SET name = :name, description = :description, genre = :genre, release_date = :release_date, duration = :duration, rating = :rating WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $res = $stmt->execute([
                ':name' => $name,
                ':description' => $description,
                ':genre' => $genre,
                ':release_date' => $releaseDate,
                ':duration' => $duration,
                ':rating' => $rating,
                ':id' => $id
            ]);

            if ($res) {
                echo "<script>alert('Movie updated successfully'); window.location.href = 'index.php';</script>";
            }
        }
    }
}

$sql = "SELECT * FROM movies WHERE id=" . $_GET['id'];
$stmt = $pdo->prepare($sql);
$stmt->execute();
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
            <form action="" method="post" enctype="multipart/form-data">
              <input type="hidden" name="_token" value="<?php echo $_SESSION['_token'] ?>" />
              <input type="hidden" name="id" value="<?php echo escape($_GET['id']) ?>">
              <div class="form-group">
                <label for="name">Name</label><p style="color:red"><?php echo empty($nameError) ? '' : '*'.$nameError; ?></p>
                <input type="text" class="form-control" name="name" value="<?php echo escape($result['name']) ?>">
              </div>
              <div class="form-group">
                <label for="description">Description</label><p style="color:red"><?php echo empty($descError) ? '' : '*'.$descError; ?></p>
                <textarea class="form-control" name="description" rows="8" cols="30"><?php echo escape($result['description']) ?></textarea>
              </div>
              <div class="form-group">
                <label for="genre">Genre</label><p style="color:red"><?php echo empty($genreError) ? '' : '*'.$genreError; ?></p>
                <input type="text" class="form-control" name="genre" value="<?php echo escape($result['genre']) ?>">
              </div>
              <div class="form-group">
                <label for="release_date">Release Date</label><p style="color:red"><?php echo empty($releaseDateError) ? '' : '*'.$releaseDateError; ?></p>
                <input type="date" class="form-control" name="release_date" value="<?php echo escape($result['release_date']) ?>">
              </div>
              <div class="form-group">
                <label for="duration">Duration (in minutes)</label><p style="color:red"><?php echo empty($durationError) ? '' : '*'.$durationError; ?></p>
                <input type="number" class="form-control" name="duration" value="<?php echo escape($result['duration']) ?>">
              </div>
              <div class="form-group">
                <label for="rating">Rating</label><p style="color:red"><?php echo empty($ratingError) ? '' : '*'.$ratingError; ?></p>
                <input type="number" class="form-control" name="rating" step="0.1" value="<?php echo escape($result['rating']) ?>">
              </div>
              <div class="form-group">
                <label for="image">Image</label><p style="color:red"><?php echo empty($imageError) ? '' : '*'.$imageError; ?></p>
                <img src="<?php echo escape($result['image']) ?>" alt="Movie Image" width="150" height="150"><br>
                <input type="file" name="image" value="">
              </div>
              <div class="form-group">
                <input type="submit" class="btn btn-success" value="SUBMIT">
                <a href="index.php" class="btn btn-warning">Back</a>
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
