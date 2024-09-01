<?php

require '../config/config.php';

$stmt = $pdo->prepare("DELETE FROM seats WHERE id=".$_GET['id']);

$stmt->execute();

header('Location: seat_manage.php');

?>