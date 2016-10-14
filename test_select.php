<?php 
include 'QueryBuilder.php';

$fakeJson = '{
	"entity" : "people",
	"fields" : {
		"nom" : "nom",
		"prenom" : "prenom"
	},
	"where" : {
		"id_people" : "23"
	}

}';

QueryBuilder::select($fakeJson);
?>