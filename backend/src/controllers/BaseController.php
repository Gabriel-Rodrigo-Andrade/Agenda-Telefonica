<?php

class BaseController
{
  protected function resposta($dados, $status = 200, $jsonOptions = JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
  {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($dados, $jsonOptions);
    exit;
  }
}
