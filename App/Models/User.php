<?php
namespace App\Models;
use App\Config\Database;
use PDOException;
class User
{
    private $pdo;
    public function __construct(){
        $this->pdo = Database::getInstance()->getConnection();
    }
    
    public function register($apiData){
        try{
            $sql = "INSERT INTO users(`name`,email,`password`,`role`) VALUES (:name,:email,:password,:role)";
            $stmt= $this->pdo->prepare($sql);
            $hashPassword= password_hash($apiData['password'],PASSWORD_ARGON2ID);
            $params=[
                ':name'=> $apiData['name'],
                ':email'=> $apiData['email'],
                ':password'=> $hashPassword,
                ':role'=> $apiData['role']
            ];
            $success = $stmt->execute($params);
            if($success){
                return $this->pdo->lastInsertId();
            }
            return false;
        }
        catch(PDOException $e){
            echo json_encode("Erro durante a criação do usuário -> " . $e);
        }
    }
}
?>