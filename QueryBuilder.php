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

    public static function select($jsonRequest) { 
        $request = json_decode($jsonRequest);
        $schema = self::getSchema($request->entity);
        if ($schema) {
            $response = self::fetchQuery(self::buildSelectQuery($schema, $request));
            var_dump($response);
        }
    }

    public static function insert($jsonRequest) { 
        $request = json_decode($jsonRequest);
        $schema = self::getSchema($request->entity);
        if ($schema) {
            self::execQuery(self::buildPrimaryInsertQuery($schema, $request));
            if (isset($request->sub_values)) {
                foreach ($schema->sub_tables as $subTable) {
                    self::execQuery(self::buildSubInsertQuery($subTable, $request));
                } 
            }
        }
    }
    
    public static function update($jsonRequest) {
        $request = json_decode($jsonRequest);
        $schema = self::getSchema($request->entity);
        if ($schema) {
            self::changeStatutLastRow($schema, $request);
            self::execQuery(self::buildPrimaryUpdateQuery($schema, $request));
            if (isset($request->sub_values)) {
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
    * generate id for entity (id_people...)
    */
    public static function generateIdEntity($schema) {
        $key = $schema->key;
        $query = 'SELECT ' . $key . ' FROM ' . $schema->table_name . ' ORDER BY ' . $key . ' DESC LIMIT 1';
        $lastId = self::fetchQuery($query);
        if ($lastId[$key]) {
            return $lastId[$key] + 1;
        }
        return 1;
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
<<<<<<< Updated upstream
=======
    * build select query
    */
    public static function buildSelectQuery($schema, $request) {
        $query = 'SELECT ';
        if ($request->fields == '*') {
            $query .= '*';
        } else {
            foreach ($request->fields as $field) {
                end($request->fields);
                if ($field === key($request->fields)){
                    $query .= $field;
                } else {
                    $query .= $field . ',';
                }
            }    
        }
        $query .= ' FROM ' . $request->entity;
        $query .= self::getWhere($request);
        return $query;
    }

    /**
    * build where string
    */
    public static function getWhere($request) { 
        $where = ' WHERE ';
        foreach ($request->where as $key => $value) {
            end($request->where);
            if ($key === key($request->where)){
                $where .= $key . '=' . '"' . $value . '"';
            } else {
                $where .= $key . '=' . '"' . $value . '" AND ';
            }
        }
        return $where;
    }

    /**
>>>>>>> Stashed changes
    * build primary insert query
    */
    public static function buildPrimaryInsertQuery($schema, $request) {
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
            $v = '"' . $value . '"';
            if ($field == $schema->key) {
                $v = self::generateIdEntity($schema);
            }else if (array_key_exists($field, self::COMMON_FIELDS)){
                $v = self::COMMON_FIELDS[$field];
            }
            end($request->values);
            if ($field === key($request->values)){
                $query .= $v;
            } else {
                $query .= $v . ',';
            }
        }
        $query .= ')';
        return $query;
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
        $query .= " FROM " . $schema->table_name . " WHERE " . $schema->key . " = " . $request->key . " ORDER BY " . $schema->order . " LIMIT 1";
        return $query;
    }

    /**
    * build sub insert query
    */
    public static function buildSubInsertQuery($subTable, $request) {
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
        $query .= ")VALUES (";
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
        $query .= ')';
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
    * fetch query
    */
    public static function fetchQuery($query) {
        $db = self::getDb();
        $q = $db->query($query);
        return $q->fetch();
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