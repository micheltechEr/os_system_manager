<?php
// A rota agora aponta para a Classe e o Método corretos
$router->post("/api/registeruser",'App\Controllers\Api\UserController@registerUser');
$router->post("/api/loginuser",'App\Controllers\Api\UserController@loginUser');
$router->patch("/api/updateuser/{id}",'App\Controllers\Api\UserController@updateByPatch');
$router->get("/api/getuserinfo/{id}",'App\Controllers\Api\UserController@getUserInfoById');
$router->delete("/api/deleteuser/{id}",'App\Controllers\Api\UserController@deleteById');

?>