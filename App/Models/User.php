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
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['password'] = $row['password'];
            $_SESSION['role'] = $row['role'];
            return $row['id'];
        }
        catch(PDOException $e){
            echo json_encode("Aconteceu algum erro durante o login e a senha ". $e);
            return false;
        }
    }
    public function update($id,$apiData){
        if(empty($apiData)){
                return 0;
           }
           $allowedFields = ['name','email','password','role'];

           $setClauses = [];
           $params = [];

           foreach( $apiData as $key => $value ){
                if(in_array($key, $allowedFields)){
                    $setClauses[] = "`$key` = :$key";

                    if($key === 'password'){
                        $params[":$key"] = password_hash($value, PASSWORD_ARGON2ID);
                     }   

                    else if ($key === 'email'){
                        if(!filter_var($value, FILTER_VALIDATE_EMAIL)){
                            return 0;
                        }
                        $params[":$key"] = $value;
                    }
                    else{
                        $params[":$key"] = $value;
                    }
                }
           }
            if(empty($setClauses)) {
                return 0;
            }
            $sql = "UPDATE users SET " . implode(', ',$setClauses)  . " WHERE id = :id";
            $params[':id'] = $id;
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();

        }
        catch(PDOException $e){
            return 0;
        }
    }
    public function user_info($id){
        $sql = "SELECT id,name,email,role,created_at FROM users WHERE id = :id";
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e){
            return false;
        }
    }

    public function delete($id){
        $sql = "DELETE FROM users WHERE id = :id";
        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id'=>$id]);
            return $stmt->rowCount();
        }
        catch(PDOException $e){
            echo json_encode($e);
        }
    }
    public function create_session(int $id):string{
        $token = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256',$token);
        $expiresAt= date('Y-m-d H:i:s',time()+(8600 * 30));
        $sql = "INSERT INTO user_sessions(user_id,session_token,expires_at) VALUES(:user_id, :session_token, :expires_at)";

        try{
            $stmt= $this->pdo->prepare($sql);
            $sucess = $stmt->execute([
                ':user_id'=> $id,
                ':session_token'=> $hashedToken,
                'expires_at'=> $expiresAt
            ]);

            if($sucess){
                return $token;
            }
            return false;
        }
        catch(PDOException $e){
            error_log("Erro ao criar sessão");
        }
    }
} 

?>