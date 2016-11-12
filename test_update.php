<?php 
include 'QueryBuilder.php';

$fakeJson = '{
    "entity" : "people",
    "key" : "23",
    "values" : {
        "nom" : "LE_TEST_UPDATE_FINAL"
    },
    "sub_values" : {
        "entity" : "people_parents",
        "key" : "17",
        "values" : {
            "titre" : "LE_TEST_UPDATE_FINAL"
        }
    },
    "sub_values" : {
        "entity" : "people_parents",
        "key" : "17",
        "values" : {
            "titre" : "LE_TEST_UPDATE_FINAL"
        }
    }
}';

// $fakeJson = '{
//     "entity" : "people",
//     "key" : "45",
//     "values" : {
//         "nom" : "YOOO"
//     }
// }';

QueryBuilder::update($fakeJson);
?>