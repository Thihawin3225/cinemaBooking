<?php

require '../config/config.php';

$stmt = $pdo->prepare("DELETE FROM rowandprice WHERE id=".$_GET['id']);

$stmt->execute();

header('Location: rowandprice_manage.php');

?>