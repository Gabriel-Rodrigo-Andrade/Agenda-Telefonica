<?php
// Percorre o env e carrega as info guardando elas numa array associativa (key => value), dai com putenv eu posso usar getenv para chamalo
foreach (parse_ini_file(__DIR__ . '/../../.env') as $key => $value) {
  putenv("{$key}={$value}");
}

/**
 * CORS para origens diferentes: front 5173 -> back 8080
 * funciona sem, mas era uma boa pratica
 */
header('Access-Control-Allow-Origin: *'); //permite consulta de qualquer URL
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS'); //quais chamadas HTTP sao permitidas
header('Access-Control-Allow-Headers: Content-Type'); // libera a leitura do cabecalho (cabeçalho JSON utf8 vem do BaseController)

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit;
}

require_once __DIR__ . '/../src/config/db.php';

require_once __DIR__ . '/../src/exceptions/NotFoundException.php';
require_once __DIR__ . '/../src/exceptions/ValidationException.php';
require_once __DIR__ . '/../src/exceptions/DuplicateEntryException.php';
require_once __DIR__ . '/../src/exceptions/DatabaseException.php';
require_once __DIR__ . '/../src/exceptions/ExternalServiceException.php';

require_once __DIR__ . '/../src/repositories/ContactRepository.php';
require_once __DIR__ . '/../src/repositories/AdressRepository.php';
require_once __DIR__ . '/../src/repositories/PhoneRepository.php';
require_once __DIR__ . '/../src/repositories/EmailRepository.php';
require_once __DIR__ . '/../src/repositories/ActivityRepository.php';

require_once __DIR__ . '/../src/validators/Validator.php';
require_once __DIR__ . '/../src/controllers/BaseController.php';
require_once __DIR__ . '/../src/controllers/DashboardController.php';
require_once __DIR__ . '/../src/controllers/ContactController.php';
require_once __DIR__ . '/../src/controllers/AdressController.php';
require_once __DIR__ . '/../src/controllers/PhoneController.php';
require_once __DIR__ . '/../src/controllers/CepController.php';

// autoload do composer para usar carbon e outras bibliotecas sem precisar fazer require manualmente
require_once __DIR__ . '/../vendor/autoload.php';

$method = $_SERVER['REQUEST_METHOD'];
// parse_url pega so o caminho
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim($uri, '/');

//regex #^enderecos/contato/(\d+)$# vai juntar url com o id do grupo((\d+))
if ($method === 'GET' && preg_match('#^enderecos/contato/(\d+)$#', $uri, $matches)) {
  /* $matches = url limpa em string 
  * [1] = grupo, um grupo e definido pelo o que esta dentro de () no regex
  * logo $matches[1] é só o id, sem o resto da consulta
  */
  $id = (int)$matches[1];
  $controller = new AdressController($pdo);
  $controller->getEnderecoPorContato($id);
}

if ($method === 'GET' && preg_match('#^contatos/(\d+)/enderecos$#', $uri, $matches)) {
  $id = (int)$matches[1];
  $controller = new AdressController($pdo);
  $controller->getEnderecosPorContato($id);
}

if ($method === 'PUT' && preg_match('#^enderecos/(\d+)$#', $uri, $matches)) {
  $id = (int)$matches[1];
  $controller = new AdressController($pdo);
  $controller->atualizar($id);
}

if ($method === 'DELETE' && preg_match('#^enderecos/(\d+)$#', $uri, $matches)) {
  $id = (int)$matches[1];
  $controller = new AdressController($pdo);
  $controller->deletar($id);
}

if ($method === 'GET' && preg_match('#^telefones/contato/(\d+)$#', $uri, $matches)) {
  $id = (int)$matches[1];
  $controller = new PhoneController($pdo);
  $controller->getTelefonesPorContato($id);
}

if ($method === 'PUT' && preg_match('#^telefones/(\d+)$#', $uri, $matches)) {
  $id = (int)$matches[1];
  $controller = new PhoneController($pdo);
  $controller->atualizar($id);
}

if ($method === 'DELETE' && preg_match('#^contatos/(\d+)$#', $uri, $matches)) {
  $id = (int)$matches[1];
  $controller = new ContactController($pdo);
  $controller->deletar($id);
}

if ($method === 'PUT' && preg_match('#^contatos/(\d+)$#', $uri, $matches)) {
  $id = (int)$matches[1];
  $controller = new ContactController($pdo);
  $controller->atualizar($id);
}


// chamadas sem id passam por aqui, visto que nao precisam de formatacao de id
$routes = [
  'GET' => [
    'contatos' => function () {
      global $pdo;
      $controller = new ContactController($pdo);
      $controller->listar();
    },
    'contatos/recentes' => function () {
      global $pdo;
      $controller = new DashboardController($pdo);
      $controller->getContatosRecentes();
    },
    'atividades/recentes' => function () {
      global $pdo;
      $controller = new DashboardController($pdo);
      $controller->getAtividadesRecentes();
    },
    'enderecos/contatos' => function () {
      global $pdo;
      $controller = new ContactController($pdo);
      $controller->listar();
    },
    'telefones/contatos' => function () {
      global $pdo;
      $controller = new ContactController($pdo);
      $controller->listar();
    },
    'cep' => function () {
      if (isset($_GET['cep'])) {
        global $pdo;
        $controller = new CepController($pdo);
        $controller->buscar($_GET['cep']);
      } else {
        http_response_code(400);
        echo json_encode(['erro' => 'CEP não informado']);
      }
    }
  ],
  'POST' => [
    'contatos' => function () {
      global $pdo;
      $controller = new ContactController($pdo);
      $controller->criar();
    },
    'enderecos' => function () {
      global $pdo;
      $controller = new AdressController($pdo);
      $controller->criar();
    },
    'telefones' => function () {
      global $pdo;
      $controller = new PhoneController($pdo);
      $controller->criar();
    }
  ]
];

// validacao das rotas
if (isset($routes[$method][$uri])) {
  call_user_func($routes[$method][$uri]);
} else {
  http_response_code(404);
  echo json_encode(['erro' => 'Rota não encontrada']);
}
