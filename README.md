## Sobre

Sistema de ACL. Uso como base para criar meus projetos. Constuído com a framework [Laravel](https://laravel.com/), na versão 8.x e usa como front-end [Bootstrap 4.6](https://getbootstrap.com/).

**Esse é um projeto pessoal**, existem alternativos como sugerido na documentação oficial de [instalação do Laravel](https://laravel.com/docs/8.x/installation).

## Requisitos

Os requisitos para executar esse sistema pode ser encontrado na [documentação oficial do laravel](https://laravel.com/docs/8.x):

- PHP >= 7.3
- BCMath PHP Extension
- Ctype PHP Extension
- Fileinfo PHP Extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension

#### Bibliotecas (que podem ser) utilizadas nos projetos:

- [Captcha for Laravel](https://github.com/mewebstudio/captcha) Nota: Habilitar a extenção extension=gd no php.ini
- [Laravel DomPdf](https://github.com/barryvdh/laravel-dompdf) Exportação de dados para PDF
- [laravel excel export lib](https://laravel-excel.com/) Exportação de dados para XLSX e CSV
- [typeahead](https://github.com/corejavascript/typeahead.js) Criação de campo de autocompletar
- [bootstrap-datepicker](https://github.com/uxsolutions/bootstrap-datepicker) Campo de data/hora customizável compatível com bootstrap
- [Inputmask](https://github.com/RobinHerbots/Inputmask) Máscaras para os campos dos formulários
- [Bootstrap Multiselect](https://github.com/davidstutz/bootstrap-multiselect) Campo de seleção multipla de ítens compátivel com bootstrap
- Utilizo os temas do site [Bootswatch](https://bootswatch.com/) para a versão 4.6 do Bootstrap

## Instalação

Executar a migração das tabelas com o comando seed:

php artisan migrate --seed

Serão criados 4 usuários de acesso ao sistema, cada um com um perfíl de acesso diferente.

Login: adm@mail.com senha:123456, acesso total.
Login: gerente@mail.com senha:123456, acesso restrito.
Login: operador@mail.com senha:123456, acesso restrito, não pode excluir registros.
Login: leitor@mail.com senha: 123456, somente consulta.

## Funcionalidades

- Operadores* (usuários do sistema)
- Perfís de acesso
- Permissões

## Guia de intalação

Requer:

- Servidor apache com banco de dados MySQL instalado, se aplicável, conforme requisitos mínimos
- [Composer](https://getcomposer.org/download/) instalado
- [Git client](https://git-scm.com/downloads) instalado

Dica: [CMDER](https://cmder.net/) é um substituto do console (prompt) de comandos do windows que já vem com o git client dentre muitas outras funcionalidades

### clonar o reposítório

```
git clone https://github.com/erisilva/acl80.git
```

não esquecer de usar o composer update para fazer download das libs do framework

```
composer update
```

### criar o banco de dados

para mysql

```
CREATE DATABASE nome_do_banco_de_dados CHARACTER SET utf8 COLLATE utf8_general_ci;
```

### configurações iniciais

criar o arquivo .env de configurações:

```
php -r "copy('.env.example', '.env');"
```

editar o arquivo .env na pasta raiz do projeto com os dados de configuração com o banco.

gerando a key de segurança:

```
php artisan key:generate
```

iniciando o store para os anexos (se o projeto precisar):

```
php artisan storage:link
```

### migrações

Executar a migração das tabelas com o comando seed:

```
php artisan migrate --seed
```

Serão criados 4 usuários de acesso ao sistema, cada um com um perfíl de acesso diferente.

Login: adm@mail.com senha:123456, acesso total.
Login: gerente@mail.com senha:123456, acesso restrito.
Login: operador@mail.com senha:123456, acesso restrito, não pode excluir registros.
Login: leitor@mail.com senha: 123456, somente consulta.

### executando

```
php artisan serve

## Licenças

Código aberto licenciado sob a [licença MIT](https://opensource.org/licenses/MIT).