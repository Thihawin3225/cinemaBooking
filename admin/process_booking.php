<?php
session_start();
require '../config/config.php';
require '../config/common.php';

if (empty($_SESSION['userId']) || empty($_SESSION['loginTime'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['userId'];
    $hall_id = $_POST['hall_id'];
    $showtime_id = $_POST['showtime_id'];
    $seat_ids = $_POST['seat_ids'] ?? [];
    $booking_time = date('Y-m-d H:i:s');
    $status = 'booked';
    $image = '';

    // Validate showtime_id
    $sql = "SELECT id FROM showtimes WHERE id = :showtime_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':showtime_id' => $showtime_id]);
    $showtime = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$showtime) {
        echo "Invalid showtime ID.";
        exit();
    }

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            echo "File is not an image.";
            exit();
        }
        // Check if file already exists
        // if (file_exists($target_file)) {
        //     echo "Sorry, file already exists.";
        //     exit();
        // }

        // Check file size
        if ($_FILES["image"]["size"] > 5000000) { // 5MB limit
            echo "Sorry, your file is too large.";
            exit();
        }

        // Allow certain file formats
        $allowed_formats = ["jpg", "png", "jpeg", "gif"];
        if (!in_array($imageFileType, $allowed_formats)) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            exit();
        }

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = $target_file;
        } else {
            echo "Sorry, there was an error uploading your file.";
            exit();
        }
    }

    if (empty($seat_ids)) {
        echo "<script>alert('You must select at least one seat.');window.location.href = '../movedetail.php?showtime_id=" . urlencode($showtime_id) . "&hall_id=" . urlencode($hall_id) . "';</script>";
    } else {
        $pdo->beginTransaction();
        try {
            foreach ($seat_ids as $seat_id) {
                $sql = "INSERT INTO bookings (user_id, showtime_id, seat_id, booking_time, status, image) 
                        VALUES (:user_id, :showtime_id, :seat_id, :booking_time, :status, :image)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':user_id' => $user_id,
                    ':showtime_id' => $showtime_id,
                    ':seat_id' => $seat_id,
                    ':booking_time' => $booking_time,
                    ':status' => $status,
                    ':image' => $image
                ]);
            }
            $pdo->commit();
            echo "<script>alert('Booking is Pending');window.location.href = '../booking_success.php';</script>";
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "Failed to book seats: " . $e->getMessage();
        }
    }
}
?>
