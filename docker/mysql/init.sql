-- sem forcar o utf8 estava dando erro nos acentos do banco
SET NAMES utf8mb4;

SET CHARACTER SET utf8mb4;

CREATE DATABASE IF NOT EXISTS meu_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE meu_app;

CREATE TABLE IF NOT EXISTS contatos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    sobrenome VARCHAR(100),
    cpf VARCHAR(11) UNIQUE,
    data_nascimento DATE,
    foto_url VARCHAR(255),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS telefones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contato_id INT NOT NULL,
    tipo ENUM(
        'celular',
        'residencial',
        'comercial',
        'outro'
    ) DEFAULT 'celular',
    numero VARCHAR(20) NOT NULL,
    principal BOOLEAN DEFAULT FALSE,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (contato_id) REFERENCES contatos (id) ON DELETE CASCADE
) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS emails (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contato_id INT NOT NULL,
    tipo ENUM(
        'pessoal',
        'trabalho',
        'outro'
    ) DEFAULT 'pessoal',
    email VARCHAR(100) NOT NULL,
    principal BOOLEAN DEFAULT FALSE,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (contato_id) REFERENCES contatos (id) ON DELETE CASCADE
) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS enderecos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contato_id INT NOT NULL,
    tipo ENUM(
        'residencial',
        'comercial',
        'outro'
    ) DEFAULT 'residencial',
    logradouro VARCHAR(200),
    numero VARCHAR(20),
    complemento VARCHAR(100),
    bairro VARCHAR(100),
    cidade VARCHAR(100) NOT NULL,
    estado CHAR(2) NOT NULL,
    cep VARCHAR(10),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (contato_id) REFERENCES contatos (id) ON DELETE CASCADE
) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS atividades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(50) NOT NULL,
    descricao TEXT NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

INSERT INTO
    contatos (
        nome,
        sobrenome,
        cpf,
        data_nascimento
    )
VALUES (
        'Pelé',
        'Santos',
        '12345678901',
        '1940-10-23'
    ),
    (
        'Ronaldinho',
        'Gaúcho',
        '23456789012',
        '1980-03-21'
    ),
    (
        'Ronaldo',
        'Nazário',
        '34567890123',
        '1976-09-18'
    ),
    (
        'Zidane',
        'Zidane',
        '45678901234',
        '1972-06-23'
    ),
    (
        'Maradona',
        'Diego',
        '56789012345',
        '1960-10-30'
    ),
    (
        'Messi',
        'Lionel',
        '67890123456',
        '1987-06-24'
    ),
    (
        'Cristiano',
        'Ronaldo',
        '78901234567',
        '1985-02-05'
    ),
    (
        'Neymar',
        'Santos',
        '89012345678',
        '1992-02-05'
    ),
    (
        'Vinícius',
        'Júnior',
        '90123456789',
        '2000-07-12'
    ),
    (
        'Vinicius',
        'Vitor',
        '01234567890',
        '1999-11-22'
    );

INSERT INTO
    telefones (
        contato_id,
        tipo,
        numero,
        principal
    )
VALUES (
        1,
        'celular',
        '(11) 99999999',
        TRUE
    ),
    (
        1,
        'comercial',
        '(11) 98888888',
        FALSE
    ),
    (
        2,
        'celular',
        '(22) 99999999',
        TRUE
    ),
    (
        2,
        'residencial',
        '(22) 88888888',
        FALSE
    ),
    (
        3,
        'celular',
        '(33) 97777777',
        TRUE
    ),
    (
        4,
        'celular',
        '(44) 96666666',
        TRUE
    ),
    (
        4,
        'comercial',
        '(44) 95555555',
        FALSE
    ),
    (
        5,
        'celular',
        '(55) 94444444',
        TRUE
    ),
    (
        6,
        'celular',
        '(66) 93333333',
        TRUE
    ),
    (
        7,
        'celular',
        '(77) 92222222',
        TRUE
    ),
    (
        8,
        'celular',
        '(88) 91111111',
        TRUE
    ),
    (
        9,
        'celular',
        '(11) 99000000',
        TRUE
    ),
    (
        10,
        'celular',
        '(21) 98900000',
        TRUE
    );

INSERT INTO
    emails (
        contato_id,
        tipo,
        email,
        principal
    )
VALUES (
        1,
        'pessoal',
        'pele@teste.com',
        TRUE
    ),
    (
        2,
        'trabalho',
        'ronaldinho@teste.com',
        TRUE
    ),
    (
        2,
        'pessoal',
        'ronaldinho.gauco@teste.com',
        FALSE
    ),
    (
        3,
        'pessoal',
        'ronaldo.nazario@teste.com',
        TRUE
    ),
    (
        4,
        'pessoal',
        'zidane@teste.com',
        TRUE
    ),
    (
        5,
        'pessoal',
        'maradona@teste.com',
        TRUE
    ),
    (
        6,
        'pessoal',
        'messi@teste.com',
        TRUE
    ),
    (
        6,
        'trabalho',
        'lionel.messi@teste.com',
        FALSE
    ),
    (
        7,
        'pessoal',
        'cristiano@teste.com',
        TRUE
    ),
    (
        7,
        'trabalho',
        'cr7@teste.com',
        FALSE
    ),
    (
        8,
        'pessoal',
        'neymar@teste.com',
        TRUE
    ),
    (
        9,
        'pessoal',
        'vinicius.jr@teste.com',
        TRUE
    ),
    (
        10,
        'pessoal',
        'vitor.vini@teste.com',
        TRUE
    );

INSERT INTO
    enderecos (
        contato_id,
        tipo,
        logradouro,
        numero,
        complemento,
        bairro,
        cidade,
        estado,
        cep
    )
VALUES (
        1,
        'residencial',
        'Rua do Chute Errado',
        '1',
        'Apto 10',
        'Vila Pelé',
        'São Paulo',
        'SP',
        '01234-567'
    ),
    (
        2,
        'residencial',
        'Av. Drible Infinito',
        '100',
        NULL,
        'Ronaldinha',
        'Rio de Janeiro',
        'RJ',
        '20040-030'
    ),
    (
        2,
        'comercial',
        'Rua da Bicicleta',
        '2000',
        'Sala 1',
        'Zona Futebolística',
        'Rio de Janeiro',
        'RJ',
        '20100-100'
    ),
    (
        3,
        'residencial',
        'Rua do Fenômeno',
        '777',
        'Casa 3',
        'Vila Ronaldo',
        'Minas Gerais',
        'MG',
        '30130-001'
    ),
    (
        4,
        'residencial',
        'Av. Zidane',
        '555',
        'Apto 50',
        'Quarteirão dos Gols',
        'São Paulo',
        'SP',
        '01310-100'
    ),
    (
        5,
        'residencial',
        'Rua da Mano de Dios',
        '86',
        NULL,
        'La Bombonera',
        'Buenos Aires',
        'BA',
        '40000-000'
    ),
    (
        6,
        'residencial',
        'Av. Mais Curva',
        '10',
        'Apto 1',
        'Rosário',
        'Santa Fe',
        'SF',
        '50000-000'
    ),
    (
        7,
        'residencial',
        'Rua do Chapéu Mágico',
        '7',
        NULL,
        'Madeira',
        'Madeira',
        'MD',
        '60000-000'
    ),
    (
        8,
        'residencial',
        'Rua do Cabelo Amarelo',
        '99',
        'Apto 22',
        'Neymarlandia',
        'São Paulo',
        'SP',
        '01500-000'
    ),
    (
        9,
        'residencial',
        'Rua da Raça',
        '14',
        NULL,
        'Vinicius',
        'Rio de Janeiro',
        'RJ',
        '20500-000'
    ),
    (
        10,
        'residencial',
        'Av. do Futebol Criativo',
        '23',
        'Apto 7',
        'Vila Criativa',
        'São Paulo',
        'SP',
        '01600-000'
    );