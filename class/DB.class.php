<?php

class database
{
    private object $pdo;

    /**
     * database constructor.
     * @param $dbPath
     */
    function __construct($dbPath){
        try {
            $this->pdo = new PDO('sqlite:' . $dbPath);
        }catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param $fileName
     * @return array|false
     */
    public function fetchName($fileName){
        return $this->pdo->query('SELECT * FROM "main"."img" WHERE "file" = "'. $fileName.'" AND "hidden" = 0  LIMIT 0,1')->fetch();
    }

    /**
     * @param $fileName
     * @return false|int
     */
    public function insert($fileName){
        return $this->pdo->exec('INSERT INTO "main"."img" ("file", "time") VALUES ("'.$fileName.'", datetime())');
    }

    /**
     * @return array
     */
    public function fetchAll(){
        return $this->pdo->query('SELECT * FROM "main"."img"')->fetchAll();
    }
}