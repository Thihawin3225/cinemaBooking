<?php

require '../config/config.php';

$stmt = $pdo->prepare("DELETE FROM movies WHERE id=".$_GET['id']);

$stmt->execute();

header('Location: index.php');

?>