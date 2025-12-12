# Blog Colaborativo - Backend

Backend da aplicação de blog colaborativo desenvolvido com Laravel.

## 🏗️ Arquitetura e Tecnologias

### Arquitetura
O projeto segue uma **arquitetura modular** baseada nos princípios SOLID e Clean Code, organizando o código em módulos independentes:

```
app/
├── Modules/
│   ├── Auth/          # Módulo de autenticação
│   ├── Users/         # Módulo de usuários
│   └── Posts/         # Módulo de posts
└── Exceptions/        # Tratamento centralizado de exceções
```

Cada módulo contém:
- **Controller**: Recebe requisições HTTP
- **Service**: Lógica de negócio
- **Repository**: Acesso aos dados
- **DTO**: Validação de entrada
- **Entities**: Modelos Eloquent
- **Policies**: Autorização
- **Resources**: Formatação de respostas

### Tecnologias
- **PHP 8.2+**
- **Laravel 10** - Framework PHP
- **MySQL** - Banco de dados
- **JWT (tymon/jwt-auth)** - Autenticação
- **Swagger/OpenAPI** - Documentação da API
- **PHPUnit** - Testes unitários
- **Docker** - Containerização do MySQL

## 📦 Instalação

### 1. Instalar dependências

```bash
composer install
```

### 2. Configurar banco de dados com Docker

```bash
docker-compose up -d
```

Isso cria um container MySQL com:
- **Database**: `blog_colaborativo`
- **User**: `blog_user`
- **Password**: `blog_password`
- **Porta**: `3307`

### 3. Configurar ambiente

Copie o arquivo `.env.example` para `.env`:

```bash
cp .env.example .env
```

Configure as variáveis no `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=blog_colaborativo
DB_USERNAME=blog_user
DB_PASSWORD=blog_password
```

### 4. Gerar chaves

```bash
php artisan key:generate
php artisan jwt:secret
```

### 5. Executar migrations

```bash
php artisan migrate
```

### 6. Criar usuário de teste (opcional)

```bash
php artisan db:seed --class=UserSeeder
```

Isso cria um usuário com:
- **Email**: `teste@example.com`
- **Senha**: `senha123`

## 🚀 Como Rodar

### Desenvolvimento

```bash
php artisan serve
```

A API estará disponível em `http://localhost:8000`

### Documentação da API (Swagger)

Após iniciar o servidor, acesse:

```
http://localhost:8000/api/documentation
```

## 👤 Usuário de Teste

Após executar o seeder, você pode usar:

- **Email**: `teste@example.com`
- **Senha**: `senha123`

Ou criar um novo usuário através do endpoint:

```bash
POST /api/auth/register
{
  "name": "Seu Nome",
  "email": "seu@email.com",
  "password": "suaSenha123"
}
```
