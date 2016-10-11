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

    public static function insert($jsonRequest) { 
        $request = json_decode($jsonRequest);
        $schema = self::getSchema($request->entity);
        if ($schema) {
            self::execQuery(self::buildPrimaryInsertQuery($schema, $request));   
        }
    }
    
    public static function update($jsonRequest) {
        $request = json_decode($jsonRequest);
        $schema = self::getSchema($request->entity);
        if ($schema) {
            self::changeStatutLastRow($schema, $request);
            self::execQuery(self::buildPrimaryUpdateQuery($schema, $request));
            if ($request->sub_values) {
                foreach ($schema->sub_tables as $subTable) {
                    $tableName = $subTable->table_name;
                    $keyName = $schema->key;
                    $key = $request->sub_values->$tableName->$keyName;
                    self::changeStatutLastRow($subTable, $request->sub_values->$tableName, $tableName, $key);
                    self::execQuery(self::buildSubUpdateQuery($subTable, $request));
                }
            }
        }
    }

    /**
    * return json schema in json-object
    */
    public static function getSchema($schemaName) {
        $jsonFile = __DIR__ . self::SCHEMAS_PATH_FOLDER . '/' . $schemaName . '.json';
        if (file_exists($jsonFile)) {
            $json = file_get_contents($jsonFile);
            return json_decode($json);
        } else {
            return false;
        }
    }

    /**
    * build query change statut last row
    */
    public static function changeStatutLastRow($schema, $request, $tableName = null, $key = null) {
        if (!$tableName) {
            $tableName = $request->entity;
        }
        if (!$key) {
            $key = $request->key;
        }
        $query = 'UPDATE ' . $tableName . ' SET id_statut = 0' . ' WHERE id_statut = 1 AND ' . $schema->key .' = ' . $key . ' ORDER BY ' . $schema->order . ' LIMIT 1';
        $executedQuery = self::execQuery($query);
    }

    /**
    * build primary insert query
    */
    public static function buildPrimaryUpdateQuery($schema, $request) {
        $query = 'INSERT INTO ' . $schema->table_name . '(';
        foreach ($schema->fields as $field => $value) {
            end($schema->fields);
            if ($field === key($schema->fields)){
                $query .= $field;
            } else {
                $query .= $field . ',';
            }
        }
        $query .= " )VALUES( ";
        foreach ($request->values as $field => $value) {
            end($request->values);
            if ($field === key($request->values)){
                $query .= $value;
            } else {
                $query .= $value . ',';
            }
        }
    }

    /**
    * build primary update query
    */
    public static function buildPrimaryUpdateQuery($schema, $request) {
        //begin build string INSERT INTO + fields names
        $query = 'INSERT INTO ' . $schema->table_name . '(';
        foreach ($schema->fields as $field => $value) {
            end($schema->fields);
            if ($field === key($schema->fields)){
                $query .= $field;
            } else {
                $query .= $field . ',';
            }
        }
        //add values on query string
        $query .= ")SELECT ";
        foreach ($schema->fields as $field => $value) {
            if (array_key_exists($field, self::COMMON_FIELDS)){
                $query .= self::COMMON_FIELDS[$field] . ',';
            } elseif (array_key_exists($field, $request->values)) {
                $query .= '"' . $request->values->$field . '",';
            } else {
                $query .=  $field .',';
            }
            //remove last coma
            end($schema->fields);
            if ($field === key($schema->fields)){
                $query = substr($query,0,-1);
            }
        }
        //add sub query in string
        $key = $schema->key;
        $query .= " FROM " . $schema->table_name . " WHERE " . $schema->key . " = " . $request->values->$key . " ORDER BY " . $schema->order . " LIMIT 1";
        return $query;
    }

    /**
    * build sub update query
    */
    public static function buildSubUpdateQuery($subTable, $request) {
        //begin build string INSERT INTO + fields names
        $tableName = $subTable->table_name;
        $query = 'INSERT INTO ' . $tableName . '(';
        foreach ($subTable->fields as $field => $value) {
            end($subTable->fields);
            if ($field === key($subTable->fields)){
                $query .= $field;
            } else {
                $query .= $field . ',';
            }
        }
        //add values on query string
        $query .= ")SELECT ";
        foreach ($subTable->fields as $field => $value) {
            if (array_key_exists($field, self::COMMON_FIELDS)){
                $query .= self::COMMON_FIELDS[$field] . ',';
            } elseif (array_key_exists($field, $request->sub_values->$tableName)) {
                $query .= '"' . $request->sub_values->$tableName->$field . '",';
            } else {
                $query .=  $field .',';
            }
            //remove last coma
            end($subTable->fields);
            if ($field === key($subTable->fields)){
                $query = substr($query,0,-1);
            }
        }
        //add sub query in string
        $key = $subTable->key;
        $query .= " FROM " . $subTable->table_name . " WHERE " . $subTable->key . " = " . $request->sub_values->$tableName->$key . " ORDER BY " . $subTable->order . " LIMIT 1";
        return $query;
    }

    /**
    * execute query
    */
    public static function execQuery($query) {
        $db = self::getDb();
        $q = $db->exec($query);
        return $q;
    }

    /**
    * get PDO db object
    */
    public static function getDb(){
        try {
            return new PDO('mysql:host=localhost;dbname=' . self::DB_TABLE . ';charset=utf8', self::DB_USER, self::DB_PASSWORD);
        }
        catch (Exception $e) {
                die('Erreur : ' . $e->getMessage());
        }
    }

    
    
}

?>