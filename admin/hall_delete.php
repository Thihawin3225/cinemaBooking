<?php

require '../config/config.php';

$stmt = $pdo->prepare("DELETE FROM halls WHERE id=".$_GET['id']);

$stmt->execute();

header('Location: halls.php');

?>