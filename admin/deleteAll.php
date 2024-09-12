<?php

require '../config/config.php';

$stmt = $pdo->prepare("DELETE FROM bookings");

$stmt->execute();

header('Location: booking_manage.php');

?>