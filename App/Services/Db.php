<?php
namespace App\Services;

class Db
{
    private static $pdo = null;

    /**
     * @return \PDO
     */
    public static function getPdo()
    {
        if (!isset(self::$pdo)) {
            new self;
        }
        return self::$pdo;
    }

    private function __construct()
    {
        $dsn = 'mysql:host=' . getenv("DB_HOST") . ';dbname=' . getenv("DB_NAME") . ';charset=utf8';

        self::$pdo = new \PDO($dsn, getenv("DB_USER"), getenv("DB_PASSWORD"));
    }

    /**
     * @throws \Exception
     */
    private function __clone()
    {
        throw new \Exception("Cannot clone a singleton.");
    }

    /**
     * @throws \Exception
     */
    private function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }
}