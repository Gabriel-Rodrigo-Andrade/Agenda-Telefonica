<?php

require_once __DIR__ . '/../services/ViaCepClient.php';

// orquestrador que ta gerenciando as chamadas do service do viacep
class CepController extends BaseController
{
  private $viaCepClient;
  private $pdo;

  public function __construct($pdo = null)
  {
    $this->pdo = $pdo;
    $this->viaCepClient = new ViaCepClient();
  }

  // GET /cep?cep={cep}
  public function buscar($cep)
  {
    try {
      $endereco = $this->viaCepClient->buscarCep($cep);

      $this->resposta([
        'sucesso' => true,
        'endereco' => $endereco
      ], 200, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } catch (ValidationException $e) {
      $this->resposta([
        'sucesso' => false,
        'erros' => $e->getErrors()
      ], $e->getCode(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } catch (NotFoundException $e) {
      $this->resposta([
        'sucesso' => false,
        'erro' => $e->getMessage()
      ], $e->getCode(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } catch (ExternalServiceException $e) {
      $this->resposta([
        'sucesso' => false,
        'erro' => $e->getMessage()
      ], $e->getCode(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
  }
}
