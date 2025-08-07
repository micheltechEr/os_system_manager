<?php

// Habilita a exibição de todos os erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Carrega o autoload do Composer

require __DIR__ . '/../vendor/autoload.php';

// Cria a instância do roteador
$router = new \Bramus\Router\Router();

// Inclui o arquivo que define as rotas
require __DIR__ . '/../routes/api.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS");

// Executa o roteador
$router->run();

?>
