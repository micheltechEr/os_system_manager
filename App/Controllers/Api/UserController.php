<?php
namespace App\Controllers\Api;
use Exception;
use App\Models\User;
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
                http_response_code(200);
                echo json_encode($response);
            }
        }
        catch(Exception $e){
            http_response_code(500);
            echo json_encode(['erro' => 'Erro no servidor: ' . $e->getMessage()]);
        }
    }
}
?>