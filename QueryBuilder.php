<?php 
/**
* C.R.U.D query builder for DB, with json schemas
* By BOMBI
*/
class QueryBuilder
{
    
    const DB_USER = "root";
    const DB_PASSWORD = "root";
    const DB_TABLE = "base_nico";

    const SCHEMAS_PATH_FOLDER = "/schemas";
    const COMMON_FIELDS = array(
        'timestamp' => 'NOW()',
        'id_statut' => '"1"',
    );
    
    public static function update($jsonRequest) {
       
    }

    
    
    
}

?>