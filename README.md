# 📝 Blog API — Laravel

> Esta foi minha **primeira API** desenvolvida de forma independente, com o objetivo de validar os conhecimentos adquiridos ao longo de cursos de desenvolvimento Back-end. O projeto serviu como avaliação prática para que eu pudesse integrar a equipe de desenvolvimento Back-end do **Hub Inova**, local onde realizei meu estágio. Foi uma experiência muito importante para consolidar conceitos de API RESTful, autenticação, relacionamentos entre modelos e boas práticas com o framework Laravel.

---

## 📌 Sobre o projeto

API RESTful de um sistema de blog, onde usuários podem se cadastrar, criar postagens com parágrafos e imagens, comentar, responder comentários e salvar posts favoritos. A aplicação também conta com um sistema de notificações e controle de permissões por nível de administrador.

---

## 🚀 Tecnologias utilizadas

- **PHP** `^7.3 | ^8.0`
- **Laravel** `^8.75`
- **Laravel Sanctum** — autenticação via tokens
- **Laravel Breeze** — scaffolding de autenticação
- **SQLite** (padrão) / compatível com MySQL
- **Pest PHP** — framework de testes
- **Guzzle HTTP** — cliente HTTP

---

## ⚙️ Requisitos

- PHP >= 7.3
- Composer
- SQLite ou MySQL

---

## 🛠️ Instalação e configuração

```bash
# 1. Clone o repositório
git clone [https://github.com/gustavobeitum/BACKEND-BLOG-CHECKPOINT.git](https://github.com/gustavobeitum/BACKEND-BLOG-CHECKPOINT.git)
cd BACKEND-BLOG-CHECKPOINT

# 2. Instale as dependências
composer install

# 3. Configure o arquivo de ambiente
cp .env.example .env

# 4. Gere a chave da aplicação
php artisan key:generate

# 5. Execute as migrations
php artisan migrate

# 6. Crie o link simbólico para o storage (upload de imagens)
php artisan storage:link

# 7. Inicie o servidor
php artisan serve
```

---

## 🗄️ Banco de dados

O projeto utiliza **SQLite** por padrão (configurado no `.env.example`). Para usar MySQL, basta descomentar e preencher as variáveis `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` e `DB_PASSWORD` no arquivo `.env`.

---

## 📂 Estrutura principal

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/               # Autenticação (login, registro, reset de senha)
│   │   ├── PostController.php
│   │   ├── ParagraphController.php
│   │   ├── CommentController.php
│   │   ├── AnswerController.php
│   │   ├── SavedPostController.php
│   │   └── UserController.php
│   └── Middleware/
│       └── CheckUser.php       # Middleware de verificação de permissão
├── Models/
│   ├── User.php
│   ├── Post.php
│   ├── Paragraph.php
│   ├── Photo.php
│   ├── Comment.php
│   ├── Answer.php
│   ├── SavedPost.php
│   └── Notification.php
└── Notifications/
    └── ForUsersNotification.php
```

---

## 🔗 Endpoints da API

### 🔓 Rotas públicas (sem autenticação)

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| `POST` | `/api/user` | Cadastrar novo usuário |
| `POST` | `/api/login` | Login |
| `POST` | `/api/forgot-password` | Solicitar reset de senha |
| `GET` | `/api/user` | Listar todos os usuários |
| `GET` | `/api/user/{id}` | Visualizar usuário específico |
| `GET` | `/api/post` | Listar posts (paginado) |
| `GET` | `/api/post/{id}` | Visualizar post específico |
| `GET` | `/api/paragraph` | Listar parágrafos |
| `GET` | `/api/paragraph/{id}` | Visualizar parágrafo específico |

---

### 🔒 Rotas autenticadas (requer token Sanctum)

#### Usuário

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| `GET` | `/api/user` | Obter usuário autenticado |
| `PUT` | `/api/user/{id}` | Atualizar perfil |
| `DELETE` | `/api/user/{id}` | Deletar conta |
| `DELETE` | `/api/logout` | Logout |
| `POST` | `/api/reset-password` | Redefinir senha |

#### Posts *(requer middleware `check` — admin)*

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| `POST` | `/api/post` | Criar post (com imagem) |
| `PUT` | `/api/post/{id}` | Atualizar post |
| `DELETE` | `/api/post/{id}` | Deletar post |

#### Parágrafos *(requer middleware `check` — admin)*

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| `POST` | `/api/paragraph` | Criar parágrafo |
| `PUT` | `/api/paragraph/{id}` | Atualizar parágrafo |
| `DELETE` | `/api/paragraph/{id}` | Deletar parágrafo |

#### Comentários

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| `GET` | `/api/comment` | Listar comentários |
| `GET` | `/api/comment/{id}` | Visualizar comentário |
| `POST` | `/api/comment` | Criar comentário |
| `PUT` | `/api/comment/{id}` | Atualizar comentário |
| `DELETE` | `/api/comment/{id}` | Deletar comentário |

#### Respostas de comentários

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| `GET` | `/api/answer` | Listar respostas |
| `GET` | `/api/answer/{id}` | Visualizar resposta |
| `POST` | `/api/answer` | Responder comentário (envia notificação) |
| `PUT` | `/api/answer/{id}` | Atualizar resposta |
| `DELETE` | `/api/answer/{id}` | Deletar resposta |

#### Posts salvos

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| `GET` | `/api/savedpost` | Listar posts salvos do usuário |
| `POST` | `/api/savedpost` | Salvar/remover post dos favoritos |

---

## 🔐 Autenticação

A API utiliza **Laravel Sanctum** para autenticação via tokens. Após o login, inclua o token no header de todas as requisições autenticadas (se estiver testando pelo Postaman por exemplo):

```
Authorization: Bearer {seu_token}
```

---

## 👤 Model: User

| Campo | Tipo | Descrição |
|-------|------|-----------|
| `name` | string | Primeiro nome |
| `last_name` | string | Sobrenome |
| `username` | string | Nome de usuário |
| `image` | string | Foto de perfil |
| `birthday` | date | Data de nascimento |
| `email` | string | E-mail |
| `password` | string | Senha (hash) |
| `is_admin` | boolean | Permissão de administrador |

---

## 📄 Model: Post

| Campo | Tipo | Descrição |
|-------|------|-----------|
| `user_id` | integer | Autor do post |
| `title` | string | Título |
| `type` | string | Tipo/categoria |
| `image` | string | Imagem de capa |
| `description` | string | Descrição/resumo |

Um post pode conter vários **parágrafos**, cada um com sua própria lista de **fotos**. Também suporta **comentários**, que por sua vez podem ter **respostas**.

---

## 🔔 Notificações

Ao responder um comentário, o autor do comentário recebe uma notificação automática via o sistema de notificações do Laravel (`ForUsersNotification`).

---

## 🧪 Testes

O projeto utiliza **Pest PHP** para testes. Para executar:

```bash
php artisan test
```

Os testes cobrem os fluxos de autenticação: registro, login, verificação de e-mail e reset de senha.

---

## 📝 Licença

Este projeto está sob a licença **MIT**.
