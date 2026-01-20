<?php

class DashboardController extends BaseController
{
  private $contatoRepository;
  private $atividadeRepository;

  public function __construct($pdo)
  {
    $this->contatoRepository = new ContactRepository($pdo);
    $this->atividadeRepository = new ActivityRepository($pdo);
  }


  public function getContatosRecentes()
  {
    try {
      //ultimos 5
      $contatos = $this->contatoRepository->listarRecentes(5);
      $this->resposta($contatos, 200, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } catch (Exception $e) {
      $this->resposta(['erro' => $e->getMessage()], 500, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
  }


  public function getAtividadesRecentes()
  {
    try {
      $atividades = $this->atividadeRepository->listarRecentes(5);
      $this->resposta($atividades, 200, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } catch (Exception $e) {
      $this->resposta(['erro' => $e->getMessage()], 500, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
  }
}
