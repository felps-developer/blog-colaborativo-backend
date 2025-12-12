# Blog Colaborativo - Backend

Backend da aplicação de blog colaborativo desenvolvido com Laravel 10 e PHP 8.2, seguindo arquitetura modular.

## 📋 Requisitos

- PHP >= 8.2
- Composer
- MySQL >= 5.7 ou MariaDB >= 10.3
- Docker (opcional, mas recomendado para MySQL)
- Extensões PHP necessárias:
  - OpenSSL
  - PDO
  - Mbstring
  - Tokenizer
  - XML
  - Ctype
  - JSON
  - BCMath

> **💡 Não tem PHP/Composer instalado?** Veja o guia de instalação em [INSTALACAO.md](./INSTALACAO.md) ou use Docker para instalar dependências (veja abaixo).

## 🚀 Instalação

### 0. Instalar PHP e Composer (se necessário)

Se você não tem PHP e Composer instalados, você tem duas opções:

**Opção A: Instalar localmente**
- Veja o guia completo em [INSTALACAO.md](./INSTALACAO.md)
- Ou baixe: [PHP](https://windows.php.net/download/) e [Composer](https://getcomposer.org/Composer-Setup.exe)

**Opção B: Usar Docker (rápido)**
```bash
# Instalar dependências usando Docker
docker run --rm -v ${PWD}:/app -w /app composer:latest install
```

### 1. Configure o PATH (Windows/PowerShell)

Se o Composer não for reconhecido no PowerShell, execute:

```powershell
.\fix-path.ps1
```

Ou use o script completo de setup:

```powershell
.\setup.ps1
```

> **💡 Problema com PATH?** Veja [SOLUCAO-PATH.md](./SOLUCAO-PATH.md) para soluções permanentes.

### 2. Instale as dependências

```bash
composer install
```

> **Nota:** Certifique-se de ter PHP 8.2+ instalado. Se necessário, use `composer install --ignore-platform-reqs` para ignorar verificações de plataforma.

### 3. Configure o banco de dados com Docker (Recomendado)

**Inicie o MySQL usando Docker:**

```bash
docker-compose up -d
```

Isso irá criar um container MySQL com as seguintes credenciais:
- **Database**: `blog_colaborativo`
- **User**: `blog_user`
- **Password**: `blog_password`
- **Root Password**: `root`
- **Porta Externa**: `3307` (mapeada para 3306 interno)

**Verifique se o container está rodando:**

```bash
docker-compose ps
```

**Aguarde alguns segundos para o MySQL inicializar completamente antes de continuar.**

**Verifique os logs do container (opcional):**

```bash
docker-compose logs -f mysql
```

> **💡 Dica:** Se você não tiver Docker instalado ou preferir usar MySQL local, pule este passo e configure as credenciais do seu MySQL local no arquivo `.env`.

> **⚠️ Problema com Porta 3306?** Se você receber um erro dizendo que a porta 3306 já está em uso (provavelmente porque você tem MySQL do XAMPP rodando), o Docker Compose está configurado para usar a porta **3307** externamente. Certifique-se de usar `DB_PORT=3307` no seu arquivo `.env`. Se preferir usar o MySQL do XAMPP diretamente, use `DB_PORT=3306` e as credenciais do seu XAMPP.

### 4. Configure o ambiente

Copie o arquivo `.env.example` para `.env`:

```bash
cp .env.example .env
```

**Configure o `.env` com as variáveis necessárias para a API:**

```env
# Aplicação
APP_NAME="Blog Colaborativo"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

# Banco de Dados
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=blog_colaborativo
DB_USERNAME=blog_user
DB_PASSWORD=blog_password

# JWT
JWT_SECRET=
JWT_TTL=60
```

> **Nota:** As credenciais acima são para o Docker. Se preferir usar MySQL local, ajuste `DB_USERNAME` e `DB_PASSWORD` conforme sua instalação.

### 5. Gere as chaves necessárias

**Chave da aplicação (APP_KEY):**
```bash
php artisan key:generate
```
Esta chave é usada para criptografar dados sensíveis da aplicação.

**Chave JWT (JWT_SECRET):**
```bash
php artisan jwt:secret
```
Esta chave é usada para assinar e verificar tokens JWT de autenticação.

> **💡 Importante:** Nunca compartilhe essas chaves em repositórios públicos. Elas são geradas automaticamente e adicionadas ao arquivo `.env`.

### 6. Execute as migrations

```bash
php artisan migrate
```

### 7. Execute o seeder (opcional)

Para criar um usuário de teste, execute:

```bash
php artisan db:seed
```

Ou apenas o seeder de usuários:

```bash
php artisan db:seed --class=UserSeeder
```

Isso criará um usuário com as seguintes credenciais:
- **Email**: `teste@example.com`
- **Senha**: `senha123`

## 🏃 Como rodar

### Desenvolvimento

```bash
php artisan serve
```

A API estará disponível em `http://localhost:8000`

### Produção

Configure um servidor web (Apache/Nginx) apontando para o diretório `public` do projeto.

## 📖 Documentação da API (Swagger)

O projeto utiliza Swagger/OpenAPI para documentação interativa da API.

### Instalação

Após instalar as dependências do Composer, publique a configuração do Swagger:

```bash
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
```

### Gerar Documentação

Para gerar a documentação Swagger:

```bash
php artisan l5-swagger:generate
```

### Acessar Documentação

Após iniciar o servidor (`php artisan serve`), acesse a documentação interativa em:

**URL:** `http://localhost:8000/api/documentation`

A documentação Swagger permite:
- Visualizar todos os endpoints da API
- Testar endpoints diretamente na interface
- Ver exemplos de requisições e respostas
- Autenticar usando JWT Bearer Token

### Configuração

A configuração do Swagger está em `config/l5-swagger.php`. Você pode personalizar:
- Título e descrição da API
- URL do servidor
- Configurações de segurança (JWT)
- Opções de UI

## 📚 Endpoints da API

### Autenticação

#### POST `/api/auth/register`
Registra um novo usuário.

**Body:**
```json
{
  "name": "João Silva",
  "email": "joao@example.com",
  "password": "senha123"
}
```

**Resposta (201):**
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": {
    "id": "uuid",
    "name": "João Silva",
    "email": "joao@example.com"
  }
}
```

#### POST `/api/auth/login`
Realiza login e retorna token JWT.

**Body:**
```json
{
  "email": "joao@example.com",
  "password": "senha123"
}
```

**Resposta (200):**
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": {
    "id": "uuid",
    "name": "João Silva",
    "email": "joao@example.com"
  }
}
```

#### GET `/api/auth/me`
Retorna informações do usuário autenticado.

**Headers:**
```
Authorization: Bearer {token}
```

**Resposta (200):**
```json
{
  "id": "uuid",
  "name": "João Silva",
  "email": "joao@example.com",
  "created_at": "2024-01-01T00:00:00.000000Z",
  "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

### Posts

#### GET `/api/posts`
Lista todos os posts (público).

**Query Parameters:**
- `page` (opcional): Número da página
- `per_page` (opcional): Itens por página (padrão: 10)
- `title` (opcional): Filtrar por título
- `author_id` (opcional): Filtrar por autor

**Resposta (200):**
```json
{
  "data": [
    {
      "id": "uuid",
      "title": "Título do Post",
      "author": {
        "id": "uuid",
        "name": "João Silva",
        "email": "joao@example.com"
      },
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
    }
  ],
  "total": 10,
  "page": 1,
  "last_page": 1,
  "per_page": 10
}
```

#### GET `/api/posts/{id}`
Retorna detalhes de um post específico (público).

**Resposta (200):**
```json
{
  "id": "uuid",
  "title": "Título do Post",
  "content": "Conteúdo completo do post...",
  "author": {
    "id": "uuid",
    "name": "João Silva",
    "email": "joao@example.com"
  },
  "created_at": "2024-01-01T00:00:00.000000Z",
  "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

#### POST `/api/posts`
Cria um novo post (requer autenticação).

**Headers:**
```
Authorization: Bearer {token}
```

**Body:**
```json
{
  "title": "Título do Post",
  "content": "Conteúdo do post..."
}
```

**Resposta (201):**
```json
{
  "id": "uuid",
  "title": "Título do Post",
  "content": "Conteúdo do post...",
  "author": {
    "id": "uuid",
    "name": "João Silva",
    "email": "joao@example.com"
  },
  "created_at": "2024-01-01T00:00:00.000000Z",
  "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

#### PUT `/api/posts/{id}`
Atualiza um post (requer autenticação - apenas o autor pode editar).

**Headers:**
```
Authorization: Bearer {token}
```

**Body:**
```json
{
  "title": "Título Atualizado",
  "content": "Conteúdo atualizado..."
}
```

**Resposta (200):**
```json
{
  "id": "uuid",
  "title": "Título Atualizado",
  "content": "Conteúdo atualizado...",
  "author": {
    "id": "uuid",
    "name": "João Silva",
    "email": "joao@example.com"
  },
  "created_at": "2024-01-01T00:00:00.000000Z",
  "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

#### DELETE `/api/posts/{id}`
Exclui um post (requer autenticação - apenas o autor pode excluir).

**Headers:**
```
Authorization: Bearer {token}
```

**Resposta (200):**
```json
{
  "success": true,
  "message": "Post removido com sucesso"
}
```

## 🧪 Usuário de Teste

Após executar as migrations, você pode criar um usuário de teste de duas formas:

**Opção 1: Usando o Seeder (Recomendado)**

```bash
php artisan db:seed --class=UserSeeder
```

Isso criará um usuário com:
- **Email**: `teste@example.com`
- **Senha**: `senha123`

**Opção 2: Através da API**

```bash
POST /api/auth/register
{
  "name": "Usuário Teste",
  "email": "teste@example.com",
  "password": "senha123"
}
```

## 🏗️ Arquitetura Modular

O projeto segue os princípios SOLID e Clean Code, com arquitetura modular similar ao NestJS:

```
app/
├── Modules/
│   ├── Auth/
│   │   ├── AuthController.php
│   │   ├── AuthService.php
│   │   └── Dto/
│   │       ├── LoginDto.php
│   │       └── RegisterDto.php
│   ├── Users/
│   │   ├── UsersRepository.php
│   │   └── Entities/
│   │       └── User.php
│   └── Posts/
│       ├── PostsController.php
│       ├── PostsService.php
│       ├── PostsRepository.php
│       ├── Dto/
│       │   ├── CreatePostDto.php
│       │   └── UpdatePostDto.php
│       └── Entities/
│           └── Post.php
└── Infra/
    └── Database/
        └── DatabaseModule.php
```

### Estrutura dos Módulos

Cada módulo contém:
- **Controller**: Recebe requisições HTTP e delega para Services
- **Service**: Contém a lógica de negócio
- **Repository**: Abstrai o acesso aos dados
- **Dto/**: Data Transfer Objects para validação de entrada
- **Entities/**: Modelos de dados (Eloquent Models)

### Princípios Aplicados

- **Single Responsibility**: Cada classe tem uma única responsabilidade
- **Dependency Injection**: Dependências são injetadas via construtor
- **Interface Segregation**: Interfaces específicas para cada necessidade
- **Separation of Concerns**: Separação clara entre camadas
- **Modular Architecture**: Cada funcionalidade é um módulo independente

## 📝 Status HTTP

A API utiliza os seguintes códigos de status HTTP:

- `200` - Sucesso
- `201` - Criado com sucesso
- `400` - Erro na requisição
- `401` - Não autenticado
- `403` - Sem permissão
- `404` - Não encontrado
- `422` - Erro de validação

## 🔒 Segurança

- Senhas são criptografadas usando bcrypt
- Autenticação via JWT (JSON Web Tokens)
- Validação de dados em todas as requisições
- Soft deletes para preservar dados históricos
- Proteção contra SQL Injection através do Eloquent ORM

## 📦 Dependências Principais

- **Laravel 10**: Framework PHP
- **tymon/jwt-auth**: Autenticação JWT
- **MySQL**: Banco de dados

## 🐳 Comandos Docker

### Gerenciamento do Container

```bash
# Iniciar o banco de dados MySQL
docker-compose up -d

# Parar o banco de dados
docker-compose down

# Parar e remover volumes (apaga os dados)
docker-compose down -v

# Reiniciar o container
docker-compose restart

# Ver status dos containers
docker-compose ps
```

### Logs e Monitoramento

```bash
# Ver logs do MySQL
docker-compose logs -f mysql

# Ver logs das últimas 100 linhas
docker-compose logs --tail=100 mysql

# Ver logs em tempo real
docker-compose logs -f mysql
```

### Acesso ao Banco de Dados

```bash
# Acessar o MySQL via terminal
docker-compose exec mysql mysql -u blog_user -pblog_password blog_colaborativo

# Acessar como root
docker-compose exec mysql mysql -u root -proot

# Executar comando SQL específico
docker-compose exec mysql mysql -u blog_user -pblog_password blog_colaborativo -e "SHOW TABLES;"
```

### Troubleshooting

```bash
# Verificar se o container está rodando
docker ps | grep blog_colaborativo_mysql

# Verificar uso de recursos
docker stats blog_colaborativo_mysql

# Recriar o container do zero
docker-compose down -v
docker-compose up -d
```

## 🛠️ Comandos Úteis

```bash
# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Recriar banco de dados
php artisan migrate:fresh

# Executar testes
php artisan test
```

## 📄 Licença

Este projeto é um teste prático para o Grupo de Comunicação O POVO.

