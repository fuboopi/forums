
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
    - [Installing dependencies](#installing-dependencies)
    - [Creating the CDN](#creating-the-cdn)
        - [Setting permissions](#setting-permissions)
    - [Setting up configuration files](#setting-up-configuration-files)
        - [config.php](#configphp)
        - [db_config.php](#db_configphp)


## To Do
- [x] Post/forums Actions
- [ ] Finish DMs
- [ ] Notifications/Recent Activity Page
- [ ] Expand on search functionality
- [ ] Finish this readme

## Requirements:
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
It's recommended to create a dedicated MySQL user just to limit privileges to this database only for stuff such as security.

#### Creating a user
Login to your MySQL shell and create a new user.
```sql
CREATE USER 'forums'@'localhost' IDENTIFIED WITH mysql_native_password BY 'password';
GRANT CREATE, ALTER, DROP, INSERT, UPDATE, DELETE, SELECT, REFERENCES ON forums.* TO 'forums'@'localhost';
```
Exit the MySQL shell with `exit;`.


#### Creating the database
Making sure your current working directory is still the root of the project, create the new database.
```sh
$ mysql -u forums -pPassword forums < setup/database.sql
```
#### Note
> There should be **no** space between `-p` and your password.

You can verify if the database was made by logging into MySQL and running:
```sql
USE forums;
SHOW TABLES;
```
You should get an output like:
```sql
+-------------------+
| Tables_in_forums  |
+-------------------+
| conversations     |
| follows           |
| forum_posts       |
| forum_posts_votes |
| forums            |
| messages          |
| users             |
+-------------------+
7 rows in set (0.00 sec)
```
#### Note
> This may change in the future as things get added or removed.

### Installing Dependencies
This project only requires a few dependencies to be installed through npm. Simply run `npm install` to install them.

### Creating the CDN
The CDN (Content Delivery Network) is mainly just used to storing user uploaded files. There are a few approaches to this.
- **Option 1:** Either create a new directory in the root of this project.
- **Option 2:** Create it on a different storage medium and use a symbolic link to point it from a directory in the root of this project.

The structure of the CDN should look as such:
```
/CDN
└──forums
   ├──profile_banners
   ├──profile_pictures
   └──uploads
```
#### Setting permissions
In order for PHP to write files to the CDN, you need to ensure the permisssions are set correctly (this drove me crazy lol). Usually, www-data is the user PHP uses for uploads. Recursively change the owner and permissions for the CDN directory.
```sh
sudo chown -R www-data:www-data CDN
sudo chmod -R 775 CDN
```

### Setting up configuration files
Two main files that will have to be setup are in the `setup/` directory, `config.php` and `db_config.php`.

#### config.php
The only variables that you will need to change are `$site`, `$site_name`, `$cdnDIR` and `$cdn`. `$token_name` should not be changed unless a different column name is being used.
```php
// Variables
$site = "https://example.com";
$site_name = "forum site";

define("ROOTPATH", __DIR__);
$cdnDIR = '/CDN/forums';
$cdn = "https://cdn.example.com";

// Variables that most likely don't need to be changed.
$token_name = 'remember_me';
```
`$cdn` Can either be a subdomain, or just a directory within the same domain.


#### db_config.php
`db_config.php` contains the credentials required to connect to your MySQL database. Set it accordingly to your current setup.
```php
return [
    'server' => 'localhost',
    'username' => 'username',
    'password' => 'password',
    'name' => 'database_name',
];
```