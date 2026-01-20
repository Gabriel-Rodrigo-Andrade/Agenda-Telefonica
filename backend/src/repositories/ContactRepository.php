<?php

class ContactRepository
{
  private $pdo;

  public function __construct($pdo)
  {
    $this->pdo = $pdo;
  }

  public function listar($busca = '')
  {
    $sql = "
      SELECT 
        c.id,
        c.nome,
        c.sobrenome,
        CONCAT(c.nome, ' ', c.sobrenome) as nome_completo,
        c.cpf,
        c.data_nascimento,
        c.criado_em,
        COALESCE(e.email, 'Sem email') as email
      FROM contatos c
      LEFT JOIN emails e ON c.id = e.contato_id AND e.principal = 1
    ";

    if (!empty($busca)) {
      $sql .= " WHERE CONCAT(c.nome, ' ', c.sobrenome) LIKE :busca OR c.nome LIKE :busca OR c.sobrenome LIKE :busca";
    }

    $sql .= " ORDER BY c.nome ASC";

    $stmt = $this->pdo->prepare($sql);

    if (!empty($busca)) {
      $termoBusca = "%{$busca}%";
      $stmt->bindParam(':busca', $termoBusca, PDO::PARAM_STR);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  //Lista todos os contatos (simples)
  public function listarTodos()
  {
    $query = "
      SELECT 
        id,
        nome,
        sobrenome,
        CONCAT(nome, ' ', sobrenome) as nome_completo
      FROM contatos
      ORDER BY nome ASC
    ";

    $stmt = $this->pdo->query($query);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Busca contato por ID
  public function buscarPorId($id)
  {
    $stmt = $this->pdo->prepare("SELECT * FROM contatos WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  //Verifica duplicidade
  public function existe($id)
  {
    $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM contatos WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetchColumn() > 0;
  }

  public function cpfExiste($cpf, $excluirId = null)
  {
    if ($excluirId) {
      $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM contatos WHERE cpf = ? AND id != ?");
      $stmt->execute([$cpf, $excluirId]);
    } else {
      $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM contatos WHERE cpf = ?");
      $stmt->execute([$cpf]);
    }
    return $stmt->fetchColumn() > 0;
  }

  public function criar($nome, $sobrenome, $cpf, $dataNascimento, $fotoUrl = null)
  {
    $stmt = $this->pdo->prepare("
      INSERT INTO contatos (nome, sobrenome, cpf, data_nascimento, foto_url, criado_em, atualizado_em)
      VALUES (?, ?, ?, ?, ?, NOW(), NOW())
    ");

    $stmt->execute([$nome, $sobrenome, $cpf, $dataNascimento, $fotoUrl]);
    return $this->pdo->lastInsertId();
  }


  public function atualizar($id, $nome, $sobrenome, $dataNascimento)
  {
    $stmt = $this->pdo->prepare("
      UPDATE contatos 
      SET nome = ?, sobrenome = ?, data_nascimento = ?, atualizado_em = NOW()
      WHERE id = ?
    ");

    $stmt->execute([$nome, $sobrenome, $dataNascimento, $id]);
    return $stmt->rowCount();
  }

  public function deletar($id)
  {
    $stmt = $this->pdo->prepare("DELETE FROM contatos WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->rowCount();
  }

  //Lista contatos recentes para dashboard
  public function listarRecentes($limite = 5)
  {
    $query = "
      SELECT 
        c.id,
        c.nome,
        e.email,
        t.numero as telefone,
        c.criado_em
      FROM contatos c
      LEFT JOIN emails e ON c.id = e.contato_id AND e.principal = TRUE
      LEFT JOIN telefones t ON c.id = t.contato_id AND t.principal = TRUE
      ORDER BY c.criado_em DESC
      LIMIT {$limite}
    ";

    $stmt = $this->pdo->query($query);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
