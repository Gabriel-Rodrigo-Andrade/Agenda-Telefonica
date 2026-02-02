# Agenda Telefônica - Sistema de Gerenciamento de Contatos

Sistema completo de agenda telefônica desenvolvido com Vue 3, PHP 8.2 e MySQL, totalmente containerizado com Docker.

## Stacks Utilizadas

- **Frontend:** Vue 3 + Vite + Vue Router + Axios
- **Backend:** PHP 8.2 + Composer (Carbon para datas, GuzzleHTTP para chamadas externas, PSR-7)
- **Banco de Dados:** MySQL 8.0
- **Web Server:** Nginx
- **Containerização:** Docker + Docker Compose

### Dependências e arquivos relevantes
- `backend/src/services/ViaCepClient.php`: consumo de CEP usando GuzzleHTTP.
- `backend/src/controllers/*`: controladores (Contato, Cep, Adress, Phone, Dashboard).
- `backend/vendor/nesbot/carbon`: manipulação de data/hora.
- `backend/vendor/guzzlehttp/{guzzle,promises,psr7}`: HTTP client e PSR-7.
- `frontend/src/services/api.js` e `CepService.js`: integrações com a API e ViaCEP.

---

## Instalação e Execução

### 1. Clone o repositório

**Linux / macOS / Windows (Git Bash/PowerShell):**
```bash
git clone https://github.com/Gabriel-Rodrigo-Andrade/Agenda-Telefonica.git
cd Agenda-Telefonica
```
## 2. Configuração com .env

O projeto utiliza `.env` para variáveis de ambiente (credenciais, URLs externas, etc).

**Para começar:**
1. Copie `.env.example` → `.env` (já incluído no repo)
2. Ajuste valores conforme seu ambiente

### 3. Inicie os containers Docker

**Linux / macOS:**
```bash
docker compose up -d --build
```

**Windows (PowerShell/CMD):**
```powershell
docker compose up -d --build
```

> **Esse comando irá:**
> - Construir as imagens Docker necessárias
> - Iniciar os containers em segundo plano (MySQL, PHP, Nginx, Node/Vite)
> - Criar o banco de dados automaticamente

### 4. Acesse a aplicação

Após os containers subirem, abra seu navegador:

- **Frontend (Landing Page):** [http://localhost:5173](http://localhost:5173)
- **Backend API:** [http://localhost:8080](http://localhost:8080)

- **Caso esteja aberto atualize a página para garantir**

---

### Gerenciar containers:

**Parar todos os containers:**
```bash
docker compose down
```

**Reiniciar os containers:**
```bash
docker compose restart
```

**Ver logs em tempo real:**
```bash
# Todos os serviços
docker compose logs -f

# Serviço específico
docker compose logs -f node    # Frontend
docker compose logs -f php     # Backend
docker compose logs -f db      # Banco de dados
```

**Verificar status dos containers:**
```bash
docker compose ps
```

### Acessar os containers:

**Container PHP (Backend):**
```bash
docker compose exec php bash
```

**Container Node (Frontend):**
```bash
docker compose exec node sh
```

**Container MySQL:**
```bash
docker compose exec db bash
```
[Documentação: docker compose exec](https://docs.docker.com/reference/cli/docker/compose/exec/)

---

## Estrutura do Projeto

```
Agenda-Telefonica-testeK13/
├── backend/
│   ├── public/index.php           # Dispatcher das rotas
│   ├── src/
│   │   ├── config/db.php          # Conexão MySQL
│   │   ├── controllers/           # Adress, Cep, Contato, Dashboard, Phone
│   │   ├── exceptions/            # Tratamento de exceções
│   │   ├── repositories/          # Acesso a dados (Contato, Atividade, etc.)
│   │   ├── services/ViaCepClient.php
│   │   └── validators/            # Validações de entrada
│   ├── vendor/                    # Dependências (Carbon, Guzzle, PSR-7)
│   └── composer.json
├── frontend/
│   ├── src/
│   │   ├── assets/icons/          # Ícones usados na UI
│   │   ├── components/            # AdressForm, HomeHeader, Footer, etc
│   │   ├── layouts/               # DefaultLayout, SystemLayout
│   │   ├── pages/                 # HomeLp, Contacts, Dashboard, Manage*
│   │   ├── router/index.js        # Rotas
│   │   ├── services/              # api.js, CepService.js
│   │   ├── styles/                # Estilos globais
│   │   ├── App.vue
│   │   └── main.js
│   ├── public/                    # Assets estáticos
│   ├── index.html
│   ├── vite.config.js
│   └── package.json
├── docker/
│   ├── php/Dockerfile             # Imagem PHP customizada
│   ├── nginx/default.conf         # Config Nginx
│   └── mysql/init.sql             # Schema inicial do banco
├── docker-compose.yml             # Orquestração dos serviços
└── README.md
```

---

## Troubleshooting

### Porta já está em uso

Se alguma porta (5173, 8080, 3307) já estiver ocupada, ajuste no arquivo [.env](.env) (ou copie de [.env.example](.env.example)) e reinicie os containers. O que cada variável faz:

- `APP_PORT`: porta exposta pelo Nginx/PHP (API). Se 8080 estiver ocupada, troque para outra (ex.: 8081) e atualize `VITE_API_URL` abaixo.
- `VITE_PORT`: porta do Vite (frontend dev). Se 5173 estiver ocupada, defina outra (ex.: 5174).
- `DB_PORT_PUBLIC`: porta do MySQL exposta no host. Se 3307 estiver ocupada, mude (ex.: 3308).
- `DB_HOST`/`DB_PORT`: host e porta interna do MySQL vistos pelo PHP (normalmente não mudam, ficam como `db:3306`).
- `DB_USER`/`DB_PASS`/`DB_NAME`/`DB_ROOT_PASS`: credenciais e banco criado no container MySQL.
- `VITE_API_URL`: URL que o frontend usa para chamar a API. Deve refletir `APP_PORT` (ex.: `http://localhost:8081/` se mudar a porta da API).
- `VIACEP_URL`: (geralmente não muda).

Depois de alterar, suba novamente:

```bash
docker compose down
docker compose up -d --build
```

### Node não executa `npm install` ou `npm run dev`
- Veja logs rodando só o serviço: `docker compose up node` (sem `-d`).
- Confirme o comando no compose: `sh -c "npm install && npm run dev -- --host 0.0.0.0"`.
- Se `node_modules` quebrou, recrie tudo: `docker compose down -v && docker compose up -d --build`.

### Vite não injeta `VITE_API_URL` no build
- `VITE_API_URL` deve estar no `.env` raiz e passado no compose (`environment` do serviço node).
- Em [frontend/vite.config.js](frontend/vite.config.js) a chave precisa ser `VITE_API_URL`.
- Depois de mudar `.env` ou `vite.config.js`, reinicie o node: `docker compose restart node`.

### Containers não iniciam

```bash
# Ver logs detalhados
docker compose logs

# Recriar containers do zero
docker compose down -v
docker compose up -d --build
```

---

## Notas

- O banco de dados é criado automaticamente pelo MySQL com o script em `docker/mysql/init.sql`

## Referencias
Design da Landing Page (HOME) == [Visual Gerado Pela Uxpilot](https://uxpilot.ai/s/adcf42a63f58209121179cd21964d655)

## Onde Aprendi
PHP: [Curso PHP Udemy - IMCOMPLETO](https://www.udemy.com/course/php-do-zero-a-maestria-com-projetos-incriveis/?kw=php+do+ze&src=sac)

Carbon:
  - [Parsing formatado (ex.: `Y-m-d`)](https://carbon.nesbot.com/docs/#api-createfromformat)
  - [Formatação de datas para string](https://carbon.nesbot.com/docs/#api-formatting)

GuzzleHTTP: 
  - [Fazer requisições GET](https://docs.guzzlephp.org/en/stable/quickstart.html#making-a-request),   
  - [Ler corpo/resposta](https://docs.guzzlephp.org/en/stable/quickstart.html#handling-responses)
  - [Tratamento de erros HTTP](https://docs.guzzlephp.org/en/stable/quickstart.html#exceptions)
  - [PSR-7 (interfaces usadas pelo Guzzle para Request/Response)](https://www.php-fig.org/psr/psr-7/#interfaces)

Docker/Compose: [Curso Docker Udemy](https://www.udemy.com/course/docker-zero-avancado/?kw=docker+completo+do+zero&src=sac&couponCode=KEEPLEARNINGBR)

Vue (Vite, Axios, Vue Router): 
  - [Faculdade](https://guarapuava.camporeal.edu.br/)
  - [Vite env](https://vitejs.dev/guide/env-and-mode.html) (`VITE_API_URL`)


MySql: [Faculdade](https://guarapuava.camporeal.edu.br/)

Outros :
  - [Array.map()](https://javascript.info/array-methods#map) (Não lembrava, usei pra transformar dados da API)
  - [Template literals](https://javascript.info/string#quotes) (Nunca tinha feito formatação de strings em JS)

Algumas dúvidas de qual o melhor modo, sintaxe para determinada ação em PHP foi usado GEPETECO

## Autor

Gabriel Rodrigo Andrade - Software Engineering Student