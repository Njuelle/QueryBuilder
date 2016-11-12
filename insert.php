<?php 
include 'QueryBuilder.php';
$json = $_POST['req'];
QueryBuilder::insert($json);
?>