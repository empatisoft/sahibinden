<?php
class Database {
    /**
     * @return null|PDO
     *
     * Veritabanı bağlantısı
     */
    private function connect()
    {
        $db = null;
        if ($db === null) {
            try
            {
                $dsn = 'mysql:host='.DB_SERVER.';dbname='.DB_NAME.';port='.DB_PORT.';charset=utf8';
                $db = new PDO($dsn, DB_USERNAME, DB_PASSWORD);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch (PDOException $e)
            {
                echo $e->getMessage();
            }
        }
        return $db;
    }

    public function row($query_string, $params = array()) {
        try {
            $query = $this->connect()->prepare($query_string);
            if(!empty($params)) {
                foreach ($params as $key => $value) {
                    $query->bindParam(':'.$key, $value);
                }
            }
            $query->execute();
            return $query->fetch(PDO::FETCH_OBJ);

        } catch(PDOException $e) {
            return $e->getMessage();
        }
    }

    public function result($query_string, $params = array()) {
        try {
            $query = $this->connect()->prepare($query_string);
            if(!empty($params)) {
                foreach ($params as $key => $value) {
                    $query->bindParam(':'.$key, $value);
                }
            }
            $query->execute();
            return $query->fetchAll(PDO::FETCH_OBJ);

        } catch(PDOException $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param $table
     * @param $values
     * @param $where
     * @return mixed
     *
     * Veri düzenleme fonksiyonu
     */
    public function update($table, $values, $where)
    {
        try {
            $values_string = "";
            $value_count = 0;
            foreach ($values as $key => $value)
            {
                if($value_count == 0)
                {
                    $values_string = $key . ' = :' . $key;
                }
                else
                {
                    $values_string = $values_string . ', ' . $key . ' = :' . $key;
                }
                $value_count++;
            }
            $where_string = "";
            $where_count = 0;
            foreach ($where as $key => $value)
            {
                if($where_count == 0)
                {
                    $where_string = ' WHERE '. $key . ' = :' . $key;
                }
                else
                {
                    $where_string = $where_string . ' AND ' . $key . ' = :' . $key;
                }
                $where_count++;
            }
            $query_string = 'UPDATE ' . $table .' SET '.$values_string . $where_string. '';
            $query = $this->connect()->prepare($query_string);
            foreach ($values as $key => &$value)
            {
                $query->bindParam(':'.$key, $value);
            }
            foreach ($where as $key => &$value)
            {
                $query->bindParam(':'.$key, $value);
            }
            return $query->execute();
        } catch(PDOException $e) {
            trigger_error('Wrong SQL: ' . $query_string . ' Error: ' . $e->getMessage(), E_USER_ERROR);
        }
    }

    public function insert($table, $values)
    {
        try {
            $values_string = "";
            $colums_string = "";
            $value_count = 0;
            foreach ($values as $key => $value)
            {
                if($value_count == 0)
                {
                    $colums_string = $key;
                    $values_string = ':' . $key;
                }
                else
                {
                    $colums_string = $colums_string . ', ' . $key;
                    $values_string = $values_string . ', :' . $key;
                }
                $value_count++;
            }
            $query_string = 'INSERT INTO ' . $table .' ('.$colums_string.') VALUES ('.$values_string.')';
            $query = $this->connect()->prepare($query_string);
            foreach ($values as $key => &$value)
            {
                $query->bindParam(':'.$key, $value);
            }
            return $query->execute();
        } catch(PDOException $e) {
            trigger_error('Wrong SQL: ' . $query_string . ' Error: ' . $e->getMessage(), E_USER_ERROR);
        }
    }

    public function total($primary_id, $table, $where = null)
    {
        if($where == null)
        {
            try {
                $query_string = 'SELECT COUNT('.$primary_id.') FROM ' . $table . '';
                $r = $this->connect()->prepare($query_string);
                $r->execute();
                $count = $r->fetch(PDO::FETCH_COLUMN);
                return $count;
            } catch(PDOException $e) {
                trigger_error('Wrong SQL: ' . $query_string . ' Error: ' . $e->getMessage(), E_USER_ERROR);
            }
        }
        else
        {
            $where_string = "";
            $where_count = 0;
            foreach ($where as $key => $value)
            {
                if($where_count == 0)
                {
                    $where_string = 'WHERE '. $key . ' = :' . $key;
                }
                else
                {
                    $where_string = $where_string . ' AND ' . $key . ' = :' . $key;
                }
                $where_count++;
            }
            try {
                $query_string = 'SELECT COUNT('.$primary_id.') FROM ' . $table . ' ' . $where_string . '';
                $r = $this->connect()->prepare($query_string);
                $r->execute($where);
                $count = $r->fetch(PDO::FETCH_COLUMN);
                return $count;
            } catch(PDOException $e) {
                trigger_error('Wrong SQL: ' . $query_string . ' Error: ' . $e->getMessage(), E_USER_ERROR);
            }
        }
    }
}