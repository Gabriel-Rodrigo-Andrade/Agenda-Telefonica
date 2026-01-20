<?php

use Carbon\Carbon;

class ContactController extends BaseController
{
  private $pdo;
  private $contatoRepository;
  private $telefoneRepository;
  private $enderecoRepository;
  private $emailRepository;
  private $atividadeRepository;

  public function __construct($pdo)
  {
    $this->pdo = $pdo;
    $this->contatoRepository = new ContactRepository($pdo);
    $this->telefoneRepository = new PhoneRepository($pdo);
    $this->enderecoRepository = new AdressRepository($pdo);
    $this->emailRepository = new EmailRepository($pdo);
    $this->atividadeRepository = new ActivityRepository($pdo);
  }
  //pagina contatos
  public function listar()
  {
    try {
      // Pega termo de busca da query string
      $busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';

      $contatos = $this->contatoRepository->listar($busca);

      // percorre o contato e busca telefones e endereço
      foreach ($contatos as &$contato) {
        $telefones = $this->telefoneRepository->buscarListaPorContato($contato['id']);

        $contato['telefone_comercial'] = '';
        $contato['telefone_residencial'] = '';
        $contato['telefone_celular'] = '';

        foreach ($telefones as $tel) {
          $contato['telefone_' . $tel['tipo']] = $tel['numero'];
        }

        $endereco = $this->enderecoRepository->buscarUltimoPorContato($contato['id']);

        if ($endereco) {
          $contato['endereco'] = trim("{$endereco['logradouro']}, {$endereco['numero']} {$endereco['complemento']} - {$endereco['bairro']}, {$endereco['cidade']}/{$endereco['estado']}");
        } else {
          $contato['endereco'] = 'Sem endereço';
        }
      }

      $this->resposta($contatos, 200, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } catch (Exception $e) {
      error_log('Erro ao listar contatos: ' . $e->getMessage());
      $this->resposta(['erro' => 'Erro ao listar contatos: ' . $e->getMessage()], 500, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
  }

  //pagina add new contact
  public function criar()
  {
    try {
      // Recebe JSON do front
      $data = json_decode(file_get_contents('php://input'), true);
      $erros = Validator::validarCamposObrigatorios($data, ['nome', 'sobrenome', 'cpf', 'email', 'tipo_email', 'data_nascimento']);

      // Valida nome e sobrenome
      if (!empty($data['nome'])) {
        $erroNome = Validator::validarNome($data['nome'], 'Nome');
        if ($erroNome) $erros['nome'] = $erroNome;
      }
      if (!empty($data['sobrenome'])) {
        $erroSobrenome = Validator::validarNome($data['sobrenome'], 'Sobrenome');
        if ($erroSobrenome) $erros['sobrenome'] = $erroSobrenome;
      }

      // Valida e limpa CPF
      $cpf = null;
      if (!empty($data['cpf'])) {
        $cpf = Validator::limparNumero($data['cpf']);
        $erroCpf = Validator::validarCPF($data['cpf'], $this->contatoRepository);
        if ($erroCpf) $erros['cpf'] = $erroCpf;
      }

      // Valida email
      if (!empty($data['email'])) {
        $erroEmail = Validator::validarEmail($data['email']);
        if ($erroEmail) $erros['email'] = $erroEmail;
      }

      if (!empty($data['data_nascimento'])) {
        try {
          // E convertido pra object carbon, por isso no final tenho que formatar para string antes de mandar pro db
          $dataNascimento = Carbon::createFromFormat('Y-m-d', $data['data_nascimento'])->format('Y-m-d');
        } catch (Exception $e) {
          $erros['data_nascimento'] = 'Data inválida. Use o formato DD/MM/AAAA';
        }
      }

      // Se tiver erros retorna
      if (!empty($erros)) {
        $this->resposta([
          'sucesso' => false,
          'mensagem' => 'Erros de validação encontrados',
          'erros' => $erros
        ], 422, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
      }

      // Inicia transacao (o beginTransaction e pra agrupar varios alteracoes no db, dai se uma falhar cancela todas)
      $this->pdo->beginTransaction();

      try {
        $contatoId = $this->contatoRepository->criar(
          $data['nome'],
          $data['sobrenome'],
          $cpf,
          $dataNascimento,
          $data['foto_url'] ?? null
        );

        $this->emailRepository->criar(
          $contatoId,
          $data['tipo_email'],
          $data['email'],
          true
        );

        $this->atividadeRepository->criar(
          'novo_contato',
          "Novo contato adicionado: {$data['nome']} {$data['sobrenome']}"
        );

        // Se deu boa publica a beginTransaction
        $this->pdo->commit();

        // Dados do contato criado se cadastro = sucess
        $this->resposta([
          'sucesso' => true,
          'mensagem' => 'Contato cadastrado com sucesso!',
          'contato' => [
            'id' => $contatoId,
            'nome' => $data['nome'],
            'sobrenome' => $data['sobrenome'],
            'email' => $data['email']
          ]
        ], 201, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
      } catch (Exception $e) {
        // Aqui e o rollBack da beginTransaction caso alguma alteracao de ruim e retorna o erro no exception
        $this->pdo->rollBack();
        throw $e;
      }
    } catch (Exception $e) {
      $this->resposta(['erro' => 'Erro ao cadastrar contato: ' . $e->getMessage()], 500, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
  }

  public function deletar($id)
  {
    try {
      $this->pdo->beginTransaction();

      $resultado = $this->contatoRepository->deletar($id);

      if ($resultado === 0) {
        $this->pdo->rollBack();
        $this->resposta(['erro' => 'Contato não encontrado'], 404, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
      }

      $this->atividadeRepository->criar(
        'contato_removido',
        'Contato removido do sistema'
      );

      $this->pdo->commit();
      $this->resposta(['sucesso' => true, 'mensagem' => 'Contato removido com sucesso.'], 200, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } catch (Exception $e) {
      $this->pdo->rollBack();
      $this->resposta(['erro' => 'Erro ao deletar contato: ' . $e->getMessage()], 500, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
  }

  public function atualizar($id)
  {
    try {
      $data = json_decode(file_get_contents('php://input'), true);
      $erros = [];

      // Valida nome
      if (!empty($data['nome'])) {
        $erroNome = Validator::validarNome($data['nome'], 'Nome');
        if ($erroNome) $erros['nome'] = $erroNome;
      }

      // Valida sobrenome
      if (!empty($data['sobrenome'])) {
        $erroSobrenome = Validator::validarNome($data['sobrenome'], 'Sobrenome');
        if ($erroSobrenome) $erros['sobrenome'] = $erroSobrenome;
      }

      // Valida email
      if (!empty($data['email'])) {
        $erroEmail = Validator::validarEmail($data['email']);
        if ($erroEmail) $erros['email'] = $erroEmail;
      }

      // Valida data de nascimento
      $dataNascimento = null;
      if (!empty($data['data_nascimento'])) {
        try {
          $dataNascimento = Carbon::createFromFormat('Y-m-d', $data['data_nascimento']);

          // Verifica se a data n e futuro
          if ($dataNascimento->isFuture() || $dataNascimento->isToday()) {
            $erros['data_nascimento'] = 'Data de nascimento não pode ser hoje ou no futuro';
          } else {
            $dataNascimento = $dataNascimento->format('Y-m-d');
          }
        } catch (Exception $e) {
          $erros['data_nascimento'] = 'Data inválida';
        }
      }

      if (!empty($erros)) {
        $this->resposta([
          'sucesso' => false,
          'mensagem' => 'Erros de validação encontrados',
          'erros' => $erros
        ], 422, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
      }

      $this->pdo->beginTransaction();

      try {
        // Atualiza contato
        $resultado = $this->contatoRepository->atualizar(
          $id,
          $data['nome'],
          $data['sobrenome'] ?? '',
          $dataNascimento
        );

        if ($resultado === 0) {
          $this->pdo->rollBack();
          $this->resposta(['erro' => 'Contato não encontrado'], 404, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        if (!empty($data['email'])) {
          $this->emailRepository->atualizarPrincipal($id, $data['email']);
        }

        $this->atividadeRepository->criar(
          'contato_editado',
          "Contato foi editado"
        );

        $this->pdo->commit();
        $this->resposta([
          'sucesso' => true,
          'mensagem' => 'Contato atualizado com sucesso!'
        ], 200, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
      } catch (Exception $e) {
        $this->pdo->rollBack();
        throw $e;
      }
    } catch (Exception $e) {
      $this->resposta(['erro' => 'Erro ao atualizar contato: ' . $e->getMessage()], 500, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
  }
}
