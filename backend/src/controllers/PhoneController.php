<?php

class PhoneController extends BaseController
{
  private $pdo;
  private $telefoneRepository;
  private $contatoRepository;
  private $atividadeRepository;

  public function __construct($pdo)
  {
    $this->pdo = $pdo;
    $this->telefoneRepository = new PhoneRepository($pdo);
    $this->contatoRepository = new ContactRepository($pdo);
    $this->atividadeRepository = new ActivityRepository($pdo);
  }

  public function getTelefonesPorContato($contatoId)
  {
    try {
      $telefones = $this->telefoneRepository->buscarPorContato($contatoId);

      if (!$telefones || !$telefones['id']) {
        $this->resposta(['erro' => 'Telefones não encontrados para o contato'], 404);
      }

      $this->resposta($telefones, 200);
    } catch (Exception $e) {
      $this->resposta(['erro' => $e->getMessage()], 500);
    }
  }

  public function criar()
  {
    try {
      $data = json_decode(file_get_contents('php://input'), true);
      $erros = Validator::validarDadosTelefones($data, $this->contatoRepository, $this->telefoneRepository);

      if (!empty($erros)) {
        $this->resposta([
          'sucesso' => false,
          'mensagem' => 'Erros de validação encontrados',
          'erros' => $erros
        ], 422);
      }

      // Ve se o contato ja tem telefones cadastrados
      if ($this->telefoneRepository->contatoTemTelefones($data['contato_id'])) {
        $this->resposta([
          'sucesso' => false,
          'mensagem' => 'Este contato já possui telefones cadastrados. Use a opção de atualização.'
        ], 409);
      }

      $this->pdo->beginTransaction();

      try {
        $telefoneId = null;

        // telefone obrigatorio
        if (!empty($data['telefone_celular'])) {
          $telefoneId = $this->telefoneRepository->criar($data['contato_id'], 'celular', $data['telefone_celular'], true);
        }

        //telefone comercial (opcional)
        if (!empty($data['telefone_comercial'])) {
          $this->telefoneRepository->criar($data['contato_id'], 'comercial', $data['telefone_comercial'], false);
        }

        //telefone residencial (opcional)
        if (!empty($data['telefone_residencial'])) {
          $this->telefoneRepository->criar($data['contato_id'], 'residencial', $data['telefone_residencial'], false);
        }

        $this->atividadeRepository->criar(
          'novo_telefone',
          "Telefone cadastrado"
        );

        $this->pdo->commit();

        $this->resposta([
          'sucesso' => true,
          'mensagem' => 'Telefones cadastrados com sucesso!',
          'telefone' => [
            'id' => $telefoneId,
            'contato_id' => $data['contato_id']
          ]
        ], 201);
      } catch (Exception $e) {
        $this->pdo->rollBack();
        throw $e;
      }
    } catch (Exception $e) {
      $this->resposta(['erro' => 'Erro ao cadastrar telefones: ' . $e->getMessage()], 500);
    }
  }

  public function atualizar($id)
  {
    try {
      $data = json_decode(file_get_contents('php://input'), true);

      // busca o telefone celular para obter o contato_id
      $atual = $this->telefoneRepository->buscarPorId($id);

      if (!$atual) {
        $this->resposta(['erro' => 'Telefone não encontrado'], 404);
      }

      $erros = Validator::validarDadosTelefones($data, $this->contatoRepository, $this->telefoneRepository, true);

      if (!empty($erros)) {
        $this->resposta([
          'sucesso' => false,
          'mensagem' => 'Erros de validação encontrados',
          'erros' => $erros
        ], 422);
      }

      $contatoId = $data['contato_id'] ?? $atual['contato_id'];

      $this->pdo->beginTransaction();

      try {
        // att ou insere o numero obrigatorio
        if (!empty($data['telefone_celular'])) {
          $existeCelular = $this->telefoneRepository->existePorContatoETipo($contatoId, 'celular');

          if ($existeCelular) {
            $this->telefoneRepository->atualizar($contatoId, 'celular', $data['telefone_celular']);
          } else {
            $this->telefoneRepository->criar($contatoId, 'celular', $data['telefone_celular'], true);
          }
        }

        // att ou insere o numero comercial (opciona)
        if (!empty($data['telefone_comercial'])) {
          $existeComercial = $this->telefoneRepository->existePorContatoETipo($contatoId, 'comercial');

          if ($existeComercial) {
            $this->telefoneRepository->atualizar($contatoId, 'comercial', $data['telefone_comercial']);
          } else {
            $this->telefoneRepository->criar($contatoId, 'comercial', $data['telefone_comercial'], false);
          }
        } else {
          // se tive vazio algum campo vai remover o que tinha antes
          $this->telefoneRepository->deletarPorContatoETipo($contatoId, 'comercial');
        }

        // att ou insere o numero residencial (opcional)
        if (!empty($data['telefone_residencial'])) {
          $existeResidencial = $this->telefoneRepository->existePorContatoETipo($contatoId, 'residencial');

          if ($existeResidencial) {
            $this->telefoneRepository->atualizar($contatoId, 'residencial', $data['telefone_residencial']);
          } else {
            $this->telefoneRepository->criar($contatoId, 'residencial', $data['telefone_residencial'], false);
          }
        } else {
          $this->telefoneRepository->deletarPorContatoETipo($contatoId, 'residencial');
        }

        $this->atividadeRepository->criar(
          'novo_telefone',
          "Telefone atualizado"
        );

        $this->pdo->commit();

        $this->resposta([
          'sucesso' => true,
          'mensagem' => 'Telefones atualizados com sucesso!',
          'telefone' => [
            'id' => $id,
            'contato_id' => $contatoId
          ]
        ], 200);
      } catch (Exception $e) {
        $this->pdo->rollBack();
        throw $e;
      }
    } catch (Exception $e) {
      $this->resposta(['erro' => 'Erro ao atualizar telefones: ' . $e->getMessage()], 500);
    }
  }

  private function validarTelefones($data, $isUpdate = false) {}
}
