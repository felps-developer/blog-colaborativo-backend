# Blog Colaborativo - Backend

Backend da aplicação de blog colaborativo desenvolvido com Laravel 10 e PHP 8.1, seguindo arquitetura modular.

## 📋 Requisitos

- PHP >= 8.1
- Composer
- MySQL >= 5.7 ou MariaDB >= 10.3
- Extensões PHP necessárias:
  - OpenSSL
  - PDO
  - Mbstring
  - Tokenizer
  - XML
  - Ctype
  - JSON
  - BCMath

## 🚀 Instalação

### 1. Instale as dependências

```bash
composer install
```

### 2. Configure o ambiente

Copie o arquivo `.env.example` para `.env`:

```bash
cp .env.example .env
```

Edite o arquivo `.env` e configure as seguintes variáveis:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog_colaborativo
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

JWT_SECRET=
JWT_TTL=60
```

### 3. Gere a chave da aplicação

```bash
php artisan key:generate
```

### 4. Gere a chave JWT

```bash
php artisan jwt:secret
```

### 5. Execute as migrations

```bash
php artisan migrate
```

## 🏃 Como rodar

### Desenvolvimento

```bash
php artisan serve
```

A API estará disponível em `http://localhost:8000`

### Produção

Configure um servidor web (Apache/Nginx) apontando para o diretório `public` do projeto.

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

Após executar as migrations, você pode criar um usuário de teste através do endpoint de registro:

```bash
POST /api/auth/register
{
  "name": "Usuário Teste",
  "email": "teste@example.com",
  "password": "senha123"
}
```

Ou você pode criar um seeder para popular dados de teste:

```bash
php artisan make:seeder UserSeeder
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

