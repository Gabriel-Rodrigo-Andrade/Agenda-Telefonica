<?php

class ActivityRepository
{
  private $pdo;

  public function __construct($pdo)
  {
    $this->pdo = $pdo;
  }

  public function criar($tipo, $descricao)
  {
    // Usa a hora do PHP em vez de NOW() do MySQL - refactor
    $agora = date('Y-m-d H:i:s');

    $stmt = $this->pdo->prepare("
      INSERT INTO atividades (tipo, descricao, criado_em)
      VALUES (?, ?, ?)
    ");

    $stmt->execute([$tipo, $descricao, $agora]);
    return $this->pdo->lastInsertId();
  }

  public function listarRecentes($limite = 10)
  {
    $query = "
      SELECT 
        a.id,
        a.tipo,
        a.descricao,
        a.criado_em
      FROM atividades a
      ORDER BY a.criado_em DESC
      LIMIT {$limite}
    ";

    $stmt = $this->pdo->query($query);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
