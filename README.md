# CakePHP Acl Plugin

[![License](https://poser.pugx.org/cakephp/acl/license.svg)](https://packagist.org/packages/abreu1234/acl)

A plugin for managing ACL in CakePHP applications.

Plugin está em faze inicial de desenvolvimento, estou iniciando com o cakephp faz pouco tempo, favor enviar sugestões
e críticas construtivas.

## Instalando via composer

Você pode instalar este plugin usando o composer
[composer](http://getcomposer.org). Adicionar a seguinte dependência em 
seu `composer.json` file:

```javascript
"require": {
	"abreu1234/acl": "dev-master"
}
```

e agora rode em seu terminal `php composer.phar update`

Carregue o plugin adicionando a seguinte linha em `config\bootstrap.php`:
```php
Plugin::load('Acl', ['bootstrap' => false, 'routes' => true]);
```

## Criando tabelas

Para criar as tabelas necessárias para o plugin usando `Migrations` 
rode o seguinte comando em seu terminal:

```
bin/cake migrations migrate -p Acl
```

## Carregando complemento Auth

Você deve iniciar o complemento `Auth` do cakephp

[(Auth cakephp)](http://book.cakephp.org/3.0/en/controllers/components/authentication.html)
[(Auth tutorial)](http://book.cakephp.org/3.0/en/tutorials-and-examples/blog-auth-example/auth.html)

## Configuração básica

Para carregar o complemento você deve adicionar o nome do seu controller de usuários
em `Controller\AppController.php` da sua aplicação

```php
$this->loadComponent('Acl.Acl', ['controllers' =>['user'=>'Users']]);
```

Caso você utilize grupos adicionar o nome do controller de grupos também

```php
$this->loadComponent('Acl.Acl', ['controllers' =>['user'=>'Users','group'=>'Groups']]);
```

## Sincronizar controllers de plugins
Para sincronizar os controllers de plugins basta adicionar a configuração a índice `plugins`
```php
$this->loadComponent('Acl.Acl', 
	[
		'controllers' =>['user'=>'Users','group'=>'Groups'],
		'plugins' => ['PluginName']
	]

);
```
Por padrão o plugin este plugin irá sincronizar os controlelrs

## Ignorando pastas e arquivos
Para ignorar alguma pasta ou arquivo durante a sincronização basta adicionar a configuração o índice `ignore`
com a seguinte sintaxe `Prefixo->Pasta/Arquivo->Action`. Para ignorar todos os prefixos ou pasta de um prefixo 
adicione `*`
```php
$this->loadComponent('Acl.Acl', [
	'controllers' =>['user'=>'Users','group'=>'Groups'],
	'plugins' => ['PluginName'],
	'ignore' => [
		'*' => [
	            '.','..','Component','AppController.php','empty',
	            '*'  => ['beforeFilter', 'afterFilter', 'initialize'],
	            'Permission'  => ['add']
	        ],
	        'Admin' => [
	        	'Users' => ['delete']
	        ]
        ]
]);
```

## Dando permissão

Para dar permissão para algum controller sem precisar do banco de dados
adicione as seguintes linhas. 

```php
$this->loadComponent('Acl.Acl', [
            'authorize' => [
                '/' => [
                    'Users' => ['index'],
                ]
            ],
            'controllers' =>['user'=>'Users']
        ]);
```

Usar o índice `authorize` com a seguinte sintaxe `Prefixo->Controller->Action` 
no exemplo acima estando dando permissão para o Controller `User` e Action `index`.
Para aplicação raiz sem prefixo utilizar `/`

Caso precise autorizar um controller dentro de um prefixo usar o nome do prefixo depois da `/` 

```php
$this->loadComponent('Acl.Acl', [
            'authorize' => [
                '/' => [
                    'Users' => ['index'],
                ],
                '/Admin' => [
                    'Users' => ['add'],
                ]
            ],
            'controllers' =>['user'=>'Users']
        ]);
```
No exemplo acima estamos dando permissão para o Controller `User` e Action `add` do prefixo `Admin`

Caso precise autorizar um plugin utilizar a seguinte sintaxe `Plugin.Prefix` user `/` para a raiz do plugin
```php
$this->loadComponent('Acl.Acl', [
            'authorize' => [
                '/' => [
                    'Users' => ['index'],
                ],
                '/Admin' => [
                    'Users' => ['add'],
                ],
                'Acl./' => [
                    'Permission' => ['index','synchronize'],
                    'UserGroupPermission' => ['index','getPermission','addAjax']
                ],
            ],
            'controllers' =>['user'=>'Users']
        ]);
```
Exemplo acima por segurança apenas utilize até você ter adicionado permissões para algum usuário ou grupo. 
Após remover as linhas
```php
'Acl./' => [
          'Permission' => ['index','synchronize'],
          'UserGroupPermission' => ['index','getPermission','addAjax']
      ],
```

## Método isAuthorized
Para fazer a validação do usuário ou grupo, use o método isAuthorized do complemento Auth. Adicione no 
arquivo `AppController.php` o seguinte código.
```php
    public function isAuthorized($user)
    {
        if(!$this->Acl->check()) {
            $this->Flash->error(__('User or group no access permission!'));
            return false;
        }

        return true;
    }
```

## Sincronizando 
Para sincronizar os controllers e actions basta ir até o endereço: `/acl/permission` e clicar no link de sincronização
é importante o usuário ter permissão de acesso ao controller `Permission` e Actions `index` e `synchronize`

## Gerenciando permissões
Para gerenciar as permissões dos usuários ou grupos bastar ir até o endereço : `/acl/user-group-permission`
Selecionar o usuário ou grupo e a as permissões.
Para funcionar o usuário é preciso ter sincronizado as permissões e ter permissão de acesso ao 
controller `UserGroupPermission` e Actions `index`, `getPermission` e `addAjax`
