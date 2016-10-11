<?php 
include 'QueryBuilder.php';

$fakeJson = '{
	"entity" : "people",
	"values" : {
		"timestamp" : "timestamp",
		"id_statut" : "statut",
		"id_people" : "id",
		"nom" : "nom",
		"prenom" : "prenom",
		"raison_sociale" : "raison_sociale",
		"vignette" : "vignette",
		"groupe" : "groupe",
		"profil" : "profil",
		"login_hash" : "login_hash",
		"pass_hash" : "password_hash"
	},
	"sub_values" : {
		"people_parents" : {
			"id_people" : "17",
			"titre" : "LE_TEST_UPDATE_FINAL"
		}
	}

}';

QueryBuilder::insert($fakeJson);
?>