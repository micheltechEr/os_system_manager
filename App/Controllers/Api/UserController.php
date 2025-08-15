<?php
namespace App\Controllers\Api;
use Exception;
use App\Models\User;
use PDOException;
class UserController{
    private $userModel;

    public function __construct(){
        $this->userModel = new User();
    }
    public function registerUser(){
        $data = json_decode(file_get_contents('php://input'),true);
        if(empty($data['name']) || empty($data['email']) || empty($data['password'])){
            http_response_code(400);
            echo json_encode("Por favor, preencha todos os dados");
            return;
        }
        try{
            $userId = $this->userModel->register($data);
            if($userId){
                $response=[
                    'status'=> 'success',
                    'message'=> 'Criado com sucesso'
                ];
                http_response_code(201);
                echo json_encode($response);
            }
        }
        catch(Exception $e){
                http_response_code(500);
                echo json_encode(['erro' => 'Erro no servidor: ' . $e->getMessage()]);
        }
    }
    public function loginUser(){
        $data = json_decode(file_get_contents('php://input'),true);
        if(empty($data['email']) || empty($data['password'])){
            http_response_code(400);
            echo json_encode("Por favor preencha os campos de e-mail ou senha");
            return;
        }
        $loginResponse = $this->userModel->login($data);
        try{
            if($loginResponse){
                $response=[
                    "status"=> 'success',
                    'message'=> 'Login efetuado com sucesso'
                ];
                // $setCookieName = 'session_cookie';
                // $token = $_COOKIE['session_token'];
                // $expiration = time() + (86400 * 30);
                // setcookie($setCookieName,$token,$expiration,'/','',true,true);


                http_response_code(200);
                echo json_encode($response);
            }
        }
        catch(Exception $e){
            http_response_code(500);
            echo json_encode(['erro' => 'Erro no servidor: ' . $e->getMessage()]);
        }
    }
    public function updateByPatch($id){
        $data = json_decode(file_get_contents('php://input'),true);
        try{
            $affectedRows = $this->userModel->update($id,$data);
            if($affectedRows > 0 ){
                $response=[
                    'status' => 'success',
                     'message' => 'Atualizado com sucesso'
                ];
                http_response_code(200);
                echo json_encode($response);
            }
            else{
                $response=[
                    'status'=> 'success',
                    'message'=> 'Nenhuma linha foi alterada'
                ];
                echo json_encode($response);
            }
        }
        catch(PDOException $e){
            http_response_code(500);
            echo json_encode("Aconteceu algo interno no servidor, erro ". $e);
        }
    }

    public function getUserInfoById($id){
        $userInfo = $this->userModel->user_info($id);
        try{
            if(empty($userInfo)){
                http_response_code(404);
                echo json_encode("Não foi possivel encontrar informacoes sobre esse usuário");
            }

            http_response_code(200);
            echo json_encode($userInfo);
        }
        catch(PDOException $e){
            http_response_code(500);
            echo json_encode("Aconteceu algum erro no servidor, tente novamente");
        }
    }
}
?>