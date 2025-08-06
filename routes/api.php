<?php

// A rota agora aponta para a Classe e o Método corretos
$router->get('/api/teste', 'App\Controllers\Api\TesteController@helloWorld');
$router->post("/api/registeruser",'App\Controllers\Api\UserController@registerUser');
?>