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
			"timestamp" : "timestamp",
			"id_statut" : "statut id",
			"id_people" : "id people",
			"parent_de_id" : "parent de",
			"titre" : "titre",
			"autorite_parentale" : "autorite parentale"
		}
	}

}';

QueryBuilder::insert($fakeJson);
?>