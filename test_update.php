<?php 
include 'QueryBuilder.php';

$fakeJson = '{
	"entity" : "people",
	"key" : "23",
	"values" : {
		"id_people" : "23",
		"nom" : "LE_TEST_UPDATE_FINAL"
	},
	"sub_values" : {
		"people_parents" : {
			"id_people" : "17",
			"titre" : "LE_TEST_UPDATE_FINAL"
		}
	}

}';

QueryBuilder::update($fakeJson);
?>