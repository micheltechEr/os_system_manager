<?php
namespace App\Models;
use App\Config\Database;
use PDOException;
use PDO;
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
            $userEmail = $apiData['email'];
            $hashPassword= password_hash($apiData['password'],PASSWORD_ARGON2ID);
            if(!filter_var($userEmail,FILTER_VALIDATE_EMAIL)){
                echo json_encode("Endereço de e-mail inválido");
                return false;
            }
            $params=[
                ':name'=> $apiData['name'],
                ':email'=> $userEmail,
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
    public function login($apiData){
        try{
            $sql= "SELECT * FROM users WHERE email = :email";
            $query = $this->pdo->prepare($sql);

            $emailUser = $apiData['email'];
            $query->execute([':email'=>$emailUser]);
            $row = $query->fetch(PDO::FETCH_ASSOC);
            if(!$row){
                echo json_encode("Esse endereco de e-mail esta incorreto ou nao posssui cadastro");
                return false;
            }
            if(!password_verify($apiData['password'],$row['password'])){
                echo json_encode("Senha incorreta, tente novamente");
                return false;
            }
            $_SESSION['id'] = $row['id'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['password'] = $row['password'];
            $_SESSION['role'] = $row['role'];
            return true;
        }
        catch(PDOException $e){
            echo json_encode("Aconteceu algum erro durante o login e a senha ". $e);
            return false;
        }
    }
    public function updateAccount($apiData){
     //   
    }
}
?>