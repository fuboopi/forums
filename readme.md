
# Forums
A messy, but functioning (hopefully) forums site

# Table of Contents
- [To Do](#to-do)
- [Requirements](#requirements)
- [Install](#install)
    - [Cloning repository](#cloning-repository)
    - [Web server examples](#web-server-examples)
        - [Nginx](#nginx)
        - [Apache](#apache)
    - [Database setup](#database-setup)
    - [Setting up configuration files](#setting-up-configuration-files)
        - [config.php](#configphp)
        - [db_config.php](#db_configphp)


## To Do
- [x] Post/forums Actions
- [] Finish DMs
- [] Notifications/Recent Activity Page
- [] Expand on search functionality
- [] i will think of more things :3

### Requirements:
- Nginx, Apache, etc...
- PHP-FPM
- MySQL, MariaDB, etc...
- Node.js and npm

## Install

All testing was done on Nginx, with PHP-FPM 8.1.

### Cloning repository
```bash
git clone https://github.com/fuboopi/forums.git
cd forums
```

### Web server examples
Depending on your setup, edit these as needed to suit your setup.
#### Nginx
```nginx
server {
	server_name example.com;
	root /path/to/root;
	index index.php;
	listen 80;

	client_max_body_size 500M;

	location / {
    try_files $uri $uri.html $uri/ @extensionless-php;
	    index index.html index.htm index.php;
	}


	location ~ \.php$ {
		try_files $uri =404;
		include fastcgi_params;
		fastcgi_param SCRIPT_FILENAME $document_root/$fastcgi_script_name;
		fastcgi_pass unix:/run/php/php8.1-fpm.sock;
		fastcgi_param FQDN true;

	}

	location @extensionless-php {
    	rewrite ^(.*)$ $1.php last;
	}
}
```

#### Apache
```apache
<VirtualHost *:80>
    ServerName example.com
    DocumentRoot /path/to/root

    LimitRequestBody 524288000

    DirectoryIndex index.php

    <FilesMatch \.php$>
        SetHandler "proxy:unix:/run/php/php8.1-fpm.sock|fcgi://localhost"
    </FilesMatch>

    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ $1.php [L]
</VirtualHost>
```

### Database setup

### Setting up configuration files
Two main files that will have to be setup are in the `setup/` directory, `config.php` and `db_config.php`.

#### config.php
The only variables that you will need to change are `$site`, `$site_name`, `$cdnDIR` and `$cdn`. `$token_name` should not be changed unless a different column name is being used.
```php
// Setup MySQL Database in ./db_config.php
$dbConfig = require 'db_config.php';
define("DB_SERVER", $dbConfig['server']);
define("DB_USERNAME", $dbConfig['username']);
define("DB_PASSWORD", $dbConfig['password']);
define("DB_NAME", $dbConfig['name']);

// Variables
$site = "https://example.com";
$site_name = "forum site";

define("ROOTPATH", __DIR__);
$cdnDIR = '/CDN/forums';
$cdn = "https://cdn.example.com";

// variables that u probably dont need to change lol
$token_name = 'remember_me';

// ------------------------------------------------------------------
```


#### db_config.php
`db_config.php` contains the credentials required to connect to your MySQL database. It's recommended to create a dedicated MySQL user just to limit privileges.
```php
return [
    'server' => 'localhost',
    'username' => 'username',
    'password' => 'password',
    'name' => 'database_name',
];
```