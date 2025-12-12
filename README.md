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

## 📋 Pré-requisitos

Antes de começar, certifique-se de ter instalado:

### 1. PHP 8.2 ou superior
- **Windows**: Baixe em [php.net/downloads](https://windows.php.net/download/)
- **Linux**: `sudo apt install php8.2` (Ubuntu/Debian) ou `sudo yum install php82` (CentOS/RHEL)
- **macOS**: `brew install php@8.2`

Verifique a instalação:
```bash
php -v
```

### 2. Composer
Gerenciador de dependências do PHP. Se não tiver instalado:

- **Download**: [getcomposer.org/download](https://getcomposer.org/download/)
- **Windows**: Baixe o instalador `Composer-Setup.exe`
- **Linux/macOS**: Execute no terminal:
  ```bash
  curl -sS https://getcomposer.org/installer | php
  sudo mv composer.phar /usr/local/bin/composer
  ```

Verifique a instalação:
```bash
composer --version
```

### 3. Docker e Docker Compose
Necessário para rodar o MySQL em container. Se não tiver instalado:

- **Docker Desktop (Windows/macOS)**: [docker.com/products/docker-desktop](https://www.docker.com/products/docker-desktop/)
- **Linux**: 
  ```bash
  # Ubuntu/Debian
  sudo apt update
  sudo apt install docker.io docker-compose
  sudo systemctl start docker
  sudo systemctl enable docker
  ```

Verifique a instalação:
```bash
docker --version
docker-compose --version
```

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

# JWT (será gerado no próximo passo)
JWT_SECRET=
```

### 4. Gerar chaves

```bash
php artisan key:generate
php artisan jwt:secret
Se perguntar sobre Override key, apenas digita yes
```

### 5. Executar migrations

```bash
php artisan migrate
```
### Caso de Erro na migrate

```bash
php artisan config:clear

Depois rode comando da migrate.
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

## 🧪 Testes

### Testes Aplicados

O projeto utiliza **testes unitários** com PHPUnit e Mockery para isolar e testar componentes individuais:

- **AuthServiceTest**: Testa a lógica de autenticação (registro, login, obtenção de usuário autenticado)
- **PostsServiceTest**: Testa a lógica de negócio de posts (criação, atualização, exclusão, listagem)
- **AuthControllerTest**: Testa os endpoints de autenticação
- **PostsControllerTest**: Testa os endpoints de posts

Os testes utilizam **mocks** para isolar as unidades testadas, não requerendo banco de dados ou conexões externas.

### Como Rodar os Testes

Execute todos os testes:

```bash
php artisan test
```

Ou usando PHPUnit diretamente:

```bash
vendor/bin/phpunit
```

Para executar apenas testes unitários:

```bash
php artisan test --testsuite=Unit
```

Para executar um arquivo de teste específico:

```bash
php artisan test tests/Unit/AuthServiceTest.php
```

Para executar um teste específico (método):

```bash
php artisan test --filter it_can_register_a_new_user
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
