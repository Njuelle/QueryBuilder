<?php 
include 'QueryBuilder.php';

$fakeJson = '{
	"entity" : "people",
	"where" : {
		"id_people" : "23"
	}

}';

QueryBuilder::insert($fakeJson);
?>