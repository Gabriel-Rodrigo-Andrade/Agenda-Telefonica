<?php

class AdressController extends BaseController
{
  private $enderecoRepository;
  private $contatoRepository;
  private $atividadeRepository;

  public function __construct($pdo)
  {
    $this->enderecoRepository = new AdressRepository($pdo);
    $this->contatoRepository = new ContactRepository($pdo);
    $this->atividadeRepository = new ActivityRepository($pdo);
  }

  //Retorna todos os endereços do contato
  public function getEnderecosPorContato($contatoId)
  {
    try {
      $enderecos = $this->enderecoRepository->buscarPorContato($contatoId);
      $this->resposta($enderecos, 200, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } catch (Exception $e) {
      $this->resposta(['erro' => $e->getMessage()], 500, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
  }

  //Retorna o endereco do contato (último cadastrado)
  public function getEnderecoPorContato($contatoId)
  {
    try {
      $endereco = $this->enderecoRepository->buscarUltimoPorContato($contatoId);

      if (!$endereco) {
        $this->resposta(['erro' => 'Endereço não encontrado para o contato'], 404, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
      }

      $this->resposta($endereco, 200, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } catch (Exception $e) {
      $this->resposta(['erro' => $e->getMessage()], 500, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
  }


  public function criar()
  {
    try {
      $data = json_decode(file_get_contents('php://input'), true);
      [$erros, $cep] = Validator::validarDadosEndereco($data, $this->contatoRepository);

      if (!empty($erros)) {
        $this->resposta([
          'sucesso' => false,
          'mensagem' => 'Erros de validação encontrados',
          'erros' => $erros
        ], 422, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
      }

      // Inserir endereço
      $enderecoId = $this->enderecoRepository->criar(
        $data['contato_id'],
        $data['tipo'],
        $data['logradouro'],
        $data['numero'],
        $data['complemento'] ?? null,
        $data['bairro'],
        $data['cidade'],
        $data['estado'],
        $cep
      );

      $this->atividadeRepository->criar(
        'novo_endereco',
        "Novo endereço cadastrado: {$data['logradouro']}, {$data['numero']}"
      );

      // Retornar sucesso
      $this->resposta([
        'sucesso' => true,
        'mensagem' => 'Endereço cadastrado com sucesso!',
        'endereco' => [
          'id' => $enderecoId,
          'contato_id' => $data['contato_id']
        ]
      ], 201, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } catch (Exception $e) {
      $this->resposta(['erro' => 'Erro ao cadastrar endereço: ' . $e->getMessage()], 500, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
  }

  public function atualizar($id)
  {
    try {
      $data = json_decode(file_get_contents('php://input'), true);

      // Verificar se endereço existe
      $atual = $this->enderecoRepository->buscarPorId($id);

      if (!$atual) {
        $this->resposta(['erro' => 'Endereço não encontrado'], 404, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
      }

      [$erros, $cep] = Validator::validarDadosEndereco($data, $this->contatoRepository, true);

      if (!empty($erros)) {
        $this->resposta([
          'sucesso' => false,
          'mensagem' => 'Erros de validação encontrados',
          'erros' => $erros
        ], 422, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
      }

      // Usar contato_id existente se n for informado
      $contatoId = $data['contato_id'] ?? $atual['contato_id'];

      $this->enderecoRepository->atualizar(
        $id,
        $contatoId,
        $data['tipo'],
        $data['logradouro'],
        $data['numero'],
        $data['complemento'] ?? null,
        $data['bairro'],
        $data['cidade'],
        $data['estado'],
        $cep
      );

      $this->atividadeRepository->criar(
        'novo_endereco',
        "Endereço atualizado: {$data['logradouro']}, {$data['numero']}"
      );

      $this->resposta([
        'sucesso' => true,
        'mensagem' => 'Endereço atualizado com sucesso!',
        'endereco' => [
          'id' => $id,
          'contato_id' => $contatoId
        ]
      ], 200, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } catch (Exception $e) {
      $this->resposta(['erro' => 'Erro ao atualizar endereço: ' . $e->getMessage()], 500, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
  }

  public function deletar($id)
  {
    try {
      $resultado = $this->enderecoRepository->deletar($id);

      if ($resultado === 0) {
        $this->resposta(['erro' => 'Endereço não encontrado'], 404, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
      }

      $this->atividadeRepository->criar(
        'novo_endereco',
        'Endereço removido do sistema'
      );

      $this->resposta(['sucesso' => true, 'mensagem' => 'Endereço removido com sucesso.'], 200, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } catch (Exception $e) {
      $this->resposta(['erro' => 'Erro ao deletar endereço: ' . $e->getMessage()], 500, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
  }
}
