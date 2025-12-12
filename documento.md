Teste Prático FullStack Pleno
Este repositório foi criado com intuito de disponibilizar os pré-requisitos e o teste prático para se tornar um Desenvolvedor FullStack Pleno do Grupo de Comunicação O POVO.

Introdução
Implemente uma aplicação de blog colaborativo, onde usuários podem se cadastrar, autenticar e gerenciar seus próprios posts.

Requisitos
Uma aplicação completa, dividida em backend (PHP) e frontend (React), que atenda aos requisitos abaixo.

Backend (PHP)
A API deve ser responsável pela autenticação e pelo CRUD de posts.

Entidades principais:
Usuário: id, nome, e-mail, senha (criptografada).
Post: id, título, conteúdo, autor_id, datas de criação/atualização.
Funcionalidades obrigatórias:
POST /auth/register – cadastro de usuário.
POST /auth/login – login com geração de token JWT.
GET /posts – listagem de posts (mostrar título, autor e data).
GET /posts/{id} – detalhes de um post (conteúdo completo).
POST /posts – criar post (apenas usuário autenticado).
PUT /posts/{id} – editar post (somente o autor pode editar).
DELETE /posts/{id} – excluir post (somente o autor pode excluir).
O que vamos observar no Backend:
Estrutura e organização do código.
Boas práticas (PSR, separação em camadas, segurança).
Uso de status HTTP corretos.
Persistência em banco de dados relacional (MySQL ou SQLite).


Frontend (React)
A interface deve permitir que usuários naveguem no blog e gerenciem seus posts.

Funcionalidades obrigatórias:
Tela de login e cadastro de usuário.
Listagem de posts disponíveis.
Visualização de um post completo.
Criar, editar e excluir posts (respeitando regras de permissão).
O que vamos observar no Frontend:
Clareza e organização dos componentes.
Navegação entre as páginas.
Boas práticas e otimização.
Tratamento de erros e loading states.
Processo de Submissão
O teste deve ser versionado e disponibilizado no GitHub do candidado.

Estruture em dois diretórios: /backend e /frontend.
Inclua um README.md com: Passos para instalar dependências, como rodar backend e frontend e usuário de teste (se aplicável).