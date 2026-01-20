<?php

class ExternalServiceException extends Exception
{
  public function __construct($message = "Erro ao consultar serviço externo", $code = 503, ?Throwable $previous = null)
  {
    parent::__construct($message, $code, $previous);
  }
}
