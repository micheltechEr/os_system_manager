<?php
// A rota agora aponta para a Classe e o Método corretos
$router->post("/api/registeruser",'App\Controllers\Api\UserController@registerUser');
$router->post("/api/loginuser",'App\Controllers\Api\UserController@loginUser');

?>