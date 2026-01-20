<?php

class DuplicateEntryException extends Exception
{
  public function __construct($message = "Registro já existe no sistema", $code = 409, ?Throwable $previous = null)
  {
    parent::__construct($message, $code, $previous);
  }
}
