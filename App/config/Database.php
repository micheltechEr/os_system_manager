<?php
namespace App\Config;
use PDO;
use Dotenv\Dotenv;

class Database{
    private static $instance = null;
    private $connection;
    private function __construct(){
        $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->load();
        $host =  $_ENV['DATABASE_URL'];
        $dbname = $_ENV['DATABASE_NAME'];
        $user = $_ENV['DATABASE_USERNAME'];
        $pass = $_ENV['DATABASE_PASSWORD'];
        $this->connection = new PDO("mysql:host=$host;dbname=$dbname","$user",$pass);
    }
    public static function getInstance(){
        if(self::$instance == null ){
            self::$instance = new Database();
        }
        return self::$instance = new Database();
    }
    public function getConnection(){
        return $this->connection;
    }
}
?>