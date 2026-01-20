<?php

class EmailRepository
{
  private $pdo;

  public function __construct($pdo)
  {
    $this->pdo = $pdo;
  }

  public function criar($contatoId, $tipo, $email, $principal = true)
  {
    $stmt = $this->pdo->prepare("
      INSERT INTO emails (contato_id, tipo, email, principal, criado_em)
      VALUES (?, ?, ?, ?, NOW())
    ");

    $stmt->execute([$contatoId, $tipo, $email, $principal ? 1 : 0]);
    return $this->pdo->lastInsertId();
  }

  public function atualizarPrincipal($contatoId, $email)
  {
    $stmt = $this->pdo->prepare("
      UPDATE emails 
      SET email = ?
      WHERE contato_id = ? AND principal = TRUE
    ");

    $stmt->execute([$email, $contatoId]);
    return $stmt->rowCount();
  }

  public function buscarPrincipalPorContato($contatoId)
  {
    $stmt = $this->pdo->prepare("
      SELECT * FROM emails 
      WHERE contato_id = ? AND principal = TRUE
    ");

    $stmt->execute([$contatoId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }
}
