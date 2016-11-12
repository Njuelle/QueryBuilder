<?php 
include 'QueryBuilder.php';

$fakeJson = '{
    "entity" : "people",
    "fields" : {
        "nom" : "nom",
        "prenom" : "prenom"
    },
    "where" : {
        "id_statut" : "1",
        "nom" : "bombi"
    }
    "sub_entity" : {
        "entity" : "people_parent",
        "fields" : {
            "titre" : "titre",
        },
        "where" : {
            "parent_de_id" : "17",
        }
    }
}';

$fakeResult = 
'
{
    0 : {
        id : dgfgf,
        prenom: dfdgg,
        nom: dfgz
    },
    1 : {
        id : dgfgf,
        prenom: dfdgg,
        nom: dfgz
    },
}
';

QueryBuilder::select($fakeJson);
?>

