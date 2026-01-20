<?php

class PhoneRepository
{
  private $pdo;

  public function __construct($pdo)
  {
    $this->pdo = $pdo;
  }

  //Busca telefones por contato (em colunas)
  public function buscarPorContato($contatoId)
  {
    $stmt = $this->pdo->prepare("
      SELECT
        MAX(CASE WHEN tipo = 'celular' THEN id END) as id,
        contato_id,
        MAX(CASE WHEN tipo = 'celular' THEN numero END) as telefone_celular,
        MAX(CASE WHEN tipo = 'comercial' THEN numero END) as telefone_comercial,
        MAX(CASE WHEN tipo = 'residencial' THEN numero END) as telefone_residencial
      FROM telefones
      WHERE contato_id = ?
      GROUP BY contato_id
    ");

    $stmt->execute([$contatoId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  //Busca telefones simples por contato
  public function buscarListaPorContato($contatoId)
  {
    $stmt = $this->pdo->prepare("
      SELECT tipo, numero 
      FROM telefones 
      WHERE contato_id = ?
    ");

    $stmt->execute([$contatoId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Verifica se contato jatem telefones
  public function contatoTemTelefones($contatoId)
  {
    $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM telefones WHERE contato_id = ?");
    $stmt->execute([$contatoId]);
    return $stmt->fetchColumn() > 0;
  }

  //Busca telefone por ID
  public function buscarPorId($id)
  {
    $stmt = $this->pdo->prepare("SELECT * FROM telefones WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  //Verifica se telefone existe para o contato daquele tipo
  public function existePorContatoETipo($contatoId, $tipo)
  {
    $stmt = $this->pdo->prepare("SELECT id FROM telefones WHERE contato_id = ? AND tipo = ?");
    $stmt->execute([$contatoId, $tipo]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  //Verifica duplicidade
  public function numeroExiste($numero, $tipo, $contatoId)
  {
    $stmt = $this->pdo->prepare("SELECT contato_id FROM telefones WHERE numero = ? AND tipo = ?");
    $stmt->execute([$numero, $tipo]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultado && $resultado['contato_id'] != $contatoId) {
      return true;
    }
    return false;
  }

  public function criar($contatoId, $tipo, $numero, $principal = false)
  {
    $stmt = $this->pdo->prepare("
      INSERT INTO telefones (contato_id, tipo, numero, principal, criado_em)
      VALUES (?, ?, ?, ?, NOW())
    ");

    $stmt->execute([$contatoId, $tipo, $numero, $principal ? 1 : 0]);
    return $this->pdo->lastInsertId();
  }

  public function atualizar($contatoId, $tipo, $numero)
  {
    $stmt = $this->pdo->prepare("UPDATE telefones SET numero = ? WHERE contato_id = ? AND tipo = ?");
    $stmt->execute([$numero, $contatoId, $tipo]);
    return $stmt->rowCount();
  }

  public function deletarPorContatoETipo($contatoId, $tipo)
  {
    $stmt = $this->pdo->prepare("DELETE FROM telefones WHERE contato_id = ? AND tipo = ?");
    $stmt->execute([$contatoId, $tipo]);
    return $stmt->rowCount();
  }
}
