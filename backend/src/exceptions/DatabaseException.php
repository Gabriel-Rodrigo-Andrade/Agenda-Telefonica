<?php

class DatabaseException extends Exception
{
  public function __construct($message = "Erro ao acessar banco de dados", $code = 500, ?Throwable $previous = null)
  {
    parent::__construct($message, $code, $previous);
  }
}
