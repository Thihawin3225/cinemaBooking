<?php

require '../config/config.php';

$stmt = $pdo->prepare("DELETE FROM showtimes WHERE id=".$_GET['id']);

$stmt->execute();

header('Location: showtimes.php');

?>