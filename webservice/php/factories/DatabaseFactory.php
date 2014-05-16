<?php

class DatabaseFactory
{

    private static $factory;
    private $database;

    public static function getFactory()
    {
        if (!self::$factory)
        {
            self::$factory = new DatabaseFactory();

        }
        return self::$factory;
    }



    public function getDatabase()
    {
        if (!$this->database)
        {
            $this->database = new Database();

        }

        return $this->database;
    }



}


?>