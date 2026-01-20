<?php

class ValidationException extends Exception
{
  private $errors;

  public function __construct($errors, $message = "Erros de validação encontrados", $code = 422, ?Throwable $previous = null)
  {
    $this->errors = $errors;
    parent::__construct($message, $code, $previous);
  }

  public function getErrors()
  {
    return $this->errors;
  }
}
