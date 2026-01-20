<?php

class Validator
{
  //Valida 11 digitos no cpf
  public static function validarCPF($cpf, $contatoRepository, $contatoIdAtual = null)
  {
    $cpfLimpo = preg_replace('/\D/', '', $cpf);

    if (strlen($cpfLimpo) !== 11) {
      return 'CPF inválido (deve conter 11 dígitos)';
    }

    // Verifica se ja existe chamando repo de contato
    if ($contatoRepository->cpfExiste($cpfLimpo, $contatoIdAtual)) {
      return 'CPF já cadastrado';
    }

    return null;
  }


  public static function validarEmail($email)
  {
    // Validacao nativca do PHP
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return 'Email inválido';
    }

    if (!preg_match('/@.+\..+/', $email)) {
      return 'Email deve conter @ e domínio válido';
    }

    return null;
  }

  //Valida nome ou sobrenome (preg_match = n pode ter numeros)
  public static function validarNome($nome, $campo = 'Nome')
  {
    if (preg_match('/\d/', $nome)) {
      return "{$campo} não pode conter números";
    }
    return null;
  }

  //Valida 8 digitos no CEP
  public static function validarCEP($cep)
  {
    $cepLimpo = preg_replace('/\D/', '', $cep);

    if (strlen($cepLimpo) !== 8) {
      return 'CEP inválido (deve conter 8 dígitos)';
    }

    return null;
  }

  //Valida estado com 2 letras
  public static function validarEstado($estado)
  {
    if (strlen($estado) !== 2) {
      return 'Estado inválido (use a sigla com 2 letras)';
    }
    return null;
  }

  // Valida telefone (10 ou 11 digitos)
  public static function validarTelefone($telefone, $tipo, $tamanhoEsperado = null)
  {
    $telefoneLimpo = preg_replace('/\D/', '', $telefone);

    if ($tamanhoEsperado) {
      if (strlen($telefoneLimpo) !== $tamanhoEsperado) {
        return "Telefone {$tipo} inválido (deve conter {$tamanhoEsperado} dígitos)";
      }
    } else {
      if (strlen($telefoneLimpo) < 10 || strlen($telefoneLimpo) > 11) {
        return "Telefone {$tipo} inválido (deve conter 10 ou 11 dígitos)";
      }
    }

    return null;
  }

  //Verifica se telefone e unic
  public static function verificarTelefoneUnico($numero, $tipo, $contatoId, $telefoneRepository)
  {
    if ($telefoneRepository->numeroExiste($numero, $tipo, $contatoId)) {
      return "Este telefone {$tipo} já está cadastrado para outro contato";
    }

    return null;
  }

  //Verifica se contato existe
  public static function verificarContatoExiste($contatoId, $contatoRepository)
  {
    if (!$contatoRepository->existe($contatoId)) {
      return 'Contato não encontrado';
    }

    return null;
  }

  //Remove caracteres sem serem numeros
  public static function limparNumero($numero)
  {
    return preg_replace('/\D/', '', $numero);
  }

  //Valida campos obrigatoios
  public static function validarCamposObrigatorios($data, $campos)
  {
    $erros = [];

    foreach ($campos as $campo) {
      if (empty($data[$campo])) {
        $erros[$campo] = "Campo obrigatório";
      }
    }

    return $erros;
  }

  public static function validarDadosEndereco($data, $contatoRepository, $isUpdate = false)
  {
    $erros = Validator::validarCamposObrigatorios($data, ['contato_id', 'cep', 'logradouro', 'numero', 'bairro', 'cidade', 'estado', 'tipo']);

    // Validar se contato existe
    if (!empty($data['contato_id'])) {
      $erroContato = Validator::verificarContatoExiste($data['contato_id'], $contatoRepository);
      if ($erroContato) $erros['contato_id'] = $erroContato;
    }

    // Validar CEP
    if (!empty($data['cep'])) {
      $erroCep = Validator::validarCEP($data['cep']);
      if ($erroCep) $erros['cep'] = $erroCep;
    }

    // Validar estado
    if (!empty($data['estado'])) {
      $erroEstado = Validator::validarEstado($data['estado']);
      if ($erroEstado) $erros['estado'] = $erroEstado;
    }

    return [$erros, Validator::limparNumero($data['cep'] ?? '')];
  }

  // Valida todos os dados de telefones
  public static function validarDadosTelefones($data, $contatoRepository, $telefoneRepository, $isUpdate = false)
  {
    $erros = [];

    // Valida contato_id
    if (empty($data['contato_id'])) {
      $erros['contato_id'] = 'Selecione um contato';
    } else {
      $erroContato = Validator::verificarContatoExiste($data['contato_id'], $contatoRepository);
      if ($erroContato) $erros['contato_id'] = $erroContato;
    }

    // Valida telefone celular (obrigatorio)
    if (empty($data['telefone_celular'])) {
      $erros['telefone_celular'] = 'Telefone celular é obrigatório';
    } else {
      $erroTelefone = Validator::validarTelefone($data['telefone_celular'], 'celular');
      if ($erroTelefone) {
        $erros['telefone_celular'] = $erroTelefone;
      } else {
        $erroUnico = Validator::verificarTelefoneUnico($data['telefone_celular'], 'celular', $data['contato_id'], $telefoneRepository);
        if ($erroUnico) $erros['telefone_celular'] = $erroUnico;
      }
    }

    // Valida telefone comercial (opcional)
    if (!empty($data['telefone_comercial'])) {
      $erroTelefone = Validator::validarTelefone($data['telefone_comercial'], 'comercial');
      if ($erroTelefone) {
        $erros['telefone_comercial'] = $erroTelefone;
      } else {
        $erroUnico = Validator::verificarTelefoneUnico($data['telefone_comercial'], 'comercial', $data['contato_id'], $telefoneRepository);
        if ($erroUnico) $erros['telefone_comercial'] = $erroUnico;
      }
    }

    // Valida telefone residencial (opcional, 10 digitos ao inves de 11)
    if (!empty($data['telefone_residencial'])) {
      $erroTelefone = Validator::validarTelefone($data['telefone_residencial'], 'residencial', 10);
      if ($erroTelefone) {
        $erros['telefone_residencial'] = $erroTelefone;
      } else {
        $erroUnico = Validator::verificarTelefoneUnico($data['telefone_residencial'], 'residencial', $data['contato_id'], $telefoneRepository);
        if ($erroUnico) $erros['telefone_residencial'] = $erroUnico;
      }
    }

    return $erros;
  }
}
