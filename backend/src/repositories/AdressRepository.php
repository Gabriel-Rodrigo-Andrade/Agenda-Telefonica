<?php

class AdressRepository
{
  private $pdo;

  public function __construct($pdo)
  {
    $this->pdo = $pdo;
  }

  //Busca todos os endereços de um contato
  public function buscarPorContato($contatoId)
  {
    $stmt = $this->pdo->prepare("
      SELECT
        id,
        contato_id,
        tipo,
        logradouro,
        numero,
        complemento,
        bairro,
        cidade,
        estado,
        cep
      FROM enderecos
      WHERE contato_id = ?
      ORDER BY criado_em DESC
    ");

    $stmt->execute([$contatoId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function buscarUltimoPorContato($contatoId)
  {
    $stmt = $this->pdo->prepare("
      SELECT
        id,
        contato_id,
        tipo,
        logradouro,
        numero,
        complemento,
        bairro,
        cidade,
        estado,
        cep
      FROM enderecos
      WHERE contato_id = ?
      ORDER BY criado_em DESC
      LIMIT 1
    ");

    $stmt->execute([$contatoId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  //Busca endereço por ID
  public function buscarPorId($id)
  {
    $stmt = $this->pdo->prepare("SELECT * FROM enderecos WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function criar($contatoId, $tipo, $logradouro, $numero, $complemento, $bairro, $cidade, $estado, $cep)
  {
    $stmt = $this->pdo->prepare("
      INSERT INTO enderecos (contato_id, tipo, logradouro, numero, complemento, bairro, cidade, estado, cep, criado_em)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->execute([$contatoId, $tipo, $logradouro, $numero, $complemento, $bairro, $cidade, $estado, $cep]);
    return $this->pdo->lastInsertId();
  }

  public function atualizar($id, $contatoId, $tipo, $logradouro, $numero, $complemento, $bairro, $cidade, $estado, $cep)
  {
    $stmt = $this->pdo->prepare("
      UPDATE enderecos
      SET contato_id = ?, tipo = ?, logradouro = ?, numero = ?, complemento = ?, bairro = ?, cidade = ?, estado = ?, cep = ?
      WHERE id = ?
    ");

    $stmt->execute([$contatoId, $tipo, $logradouro, $numero, $complemento, $bairro, $cidade, $estado, $cep, $id]);
    return $stmt->rowCount();
  }

  public function deletar($id)
  {
    $stmt = $this->pdo->prepare("DELETE FROM enderecos WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->rowCount();
  }
}
