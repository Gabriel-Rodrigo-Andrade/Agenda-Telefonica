<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ViaCepClient
{
  private $client;
  private $baseUrl;

  public function __construct()
  {
    $this->baseUrl = getenv('VIACEP_URL') ?: 'https://viacep.com.br/ws/';

    $this->client = new Client([
      'timeout' => 10,
    ]);
  }

  public function buscarCep($cep)
  {
    // Remove formatação do CEP
    $cepLimpo = preg_replace('/\D/', '', $cep);

    // Valida formato
    if (strlen($cepLimpo) !== 8) {
      throw new ValidationException(['cep' => 'CEP inválido. Deve conter 8 dígitos.']);
    }

    try {
      $url = $this->baseUrl . $cepLimpo . '/json/';
      $response = $this->client->get($url);

      $dados = json_decode($response->getBody()->getContents(), true);

      // ViaCEP retorna {"erro": true} quando CEP n existe
      if (isset($dados['erro']) && $dados['erro'] === true) {
        throw new NotFoundException('CEP não encontrado.');
      }

      return [
        'cep' => $dados['cep'] ?? '',
        'logradouro' => $dados['logradouro'] ?? '',
        'complemento' => $dados['complemento'] ?? '',
        'bairro' => $dados['bairro'] ?? '',
        'cidade' => $dados['localidade'] ?? '',
        'estado' => $dados['uf'] ?? '',
        'ibge' => $dados['ibge'] ?? '',
        'gia' => $dados['gia'] ?? '',
        'ddd' => $dados['ddd'] ?? '',
        'siafi' => $dados['siafi'] ?? ''
      ];
    } catch (GuzzleException $e) {
      throw new ExternalServiceException('Erro ao consultar ViaCEP: ' . $e->getMessage());
    }
  }
}
