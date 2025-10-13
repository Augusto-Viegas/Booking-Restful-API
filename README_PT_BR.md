# Booking System API:
API RESTful desenvolvida em Laravel 12 e php 8.3 para gerenciamento de reservas de serviços.
Foi projetada com o objetivo de ser "genérica", permitindo o seu uso em diferentes segmentos,
tais quais como clínicas, academias, hoteis etc.

# Tecnologias utilizadas:
- [Laravel 12]
- [PHP 8.3]
- [PostgreSQL]
- [Redis]
- [Docker]
- [JWT Auth]
- [phpUnit]

# Funcionalidades:
- Cadastro e autenticação de usuários e clientes
- Gerenciamento de serviços e horários
- Criação e controle de reservas com status
- Cache de consultas em Redis
- API estruturada com Resources, Policies, Enums, services etc
- Testes unitários e de feature

# Estrutura do projeto:
app/
  -Console/
    -Commands/# (Comandos customizados para API)
  -Enums/#
  -Http/
    -Controllers/
      -Api/
        -V1/# (Controladores da API)
  -Models/#
  -Policies/#
  -Providers/#
  -Services/#
  -Traits/#
Resources/#
tests/
  -Feature/#
  -Unit/#
docker/# Arquivos de configuração Docker

# Como rodar o projeto:
1)Clonar o repositório
2)Subir containers com Docker
3)Instalar dependências
4)Configurar variáveis de ambiente
5)Rodar migrations e seeders
6)Gerar chave da aplicação

# Rodando testes:
- docker exec -it booking-system-api bash
- php artisan test

# Autenticação:
A API utiliza JWT para a autenticação. Primeiramente você iria precisar de um usuário.
Faça:
- docker exec -it booking-system-api bash
- php artisan user:make
Isso irá gerar um usuário funcionário de teste com nível mais baixo de autenticação no sistema. Após isso faça:
- POST api/auth/login
  {
    'email' : 'test@email.com'
    'password' : '123456789'
  }
Isso irá gerar um token jwt para utilizar o sistema como um funcionário, por exemplo:
  {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...."
    "token_type": "bearer",
    "expires_in": 3600
  }

# Documentação da API:
- Versão em Markdown
- OpenApi/Swagger
- Swagger UI

# Boas práticas adotadas:
- Uso de Enums para status padronizados
- Respostas JSON consistentes com Resources
- Testes automatizados com phpUnit
- Organização de código seguindo DDD simplicado
- Cache + locks com Redis para performance e segurança

# Autor
Feito por Augusto Viegas



