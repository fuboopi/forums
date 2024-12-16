
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
        - [Creating a user](#creating-a-user)
        - [Creating the database](#creating-the-database)
    - [Setting up configuration files](#setting-up-configuration-files)
        - [config.php](#configphp)
        - [db_config.php](#db_configphp)


## To Do
- [x] Post/forums Actions
- [] Finish DMs
- [] Notifications/Recent Activity Page
- [] Expand on search functionality
- [] Finish this readme

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
It's recommended to create a dedicated MySQL user just to limit privileges. 

#### Creating a user
Login to your MySQL shell and create a new user.
```sql
CREATE USER 'forums'@'localhost' IDENTIFIED WITH mysql_native_password BY 'password';
GRANT CREATE, ALTER, DROP, INSERT, UPDATE, DELETE, SELECT, REFERENCES ON forums.* TO 'forums'@'localhost';
```
Log back out with `exit;` to return to your shell.


#### Creating the database
Making sure your current working directory is still the root of the project, create the new database.
```sh
$ mysql -u forums -pPassword forums < setup/database.sql
```
#### Note
> There should be no space between `-p` and your password.


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
`db_config.php` contains the credentials required to connect to your MySQL database.
```php
return [
    'server' => 'localhost',
    'username' => 'username',
    'password' => 'password',
    'name' => 'database_name',
];
```