<?php

//variaveis de acesso + fallback (com valor que estou utilizando)
$db_host = getenv('DB_HOST') ?: 'db';
$db_port = getenv('DB_PORT') ?: '3306';
$db_name = getenv('DB_NAME') ?: 'meu_app';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') ?: 'root';

//conexao com o banco passando variaveis de acesso
$pdo = new PDO(
  "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4",
  $db_user,
  $db_pass,
  [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ]
);
