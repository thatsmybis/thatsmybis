![CircleCI Build Status](https://circleci.com/gh/thatsmybis/thatsmybis.svg?style=shield "CircleCI Build Status")

# That's My BIS; a tool for loot management in World of Warcraft Classic

This project was started to give raiders a stronger voice in how loot council distributes loot, help loot council make more informed decisions, and to provide transparency in decision making back to raiders.

In general, decisions made in the development of this project should reflect those objectives. Features that may obfuscates actions and needlessly hide data from raiders should only be implemented with careful consideration, as they may harm the overall objective of this project.

We want to lessen the gap between raiders and raid management, not increase it. Raid management is there to support everyone else in the raid, not act above them.

# Some Technical Details

If you have any questions, reach out on the project's Discord server: [https://discord.gg/HWNUMKK](https://discord.gg/HWNUMKK)

Stack:

- Linux (server is running Ubuntu)
- PHP 8.0 (Laravel is the framework)
- MariaDB (almost 1:1 identical to MySQL)
- Nginx
- Javascript (jQuery, DataTables library)
- CSS (Bootstrap 4)

[Laravel Docs](https://laravel.com/docs/8.x). Laravel dictates the file structure of the project and how almost everything is implemented, so they're very useful.

Environment variables are stored in `.env`. These will generally need to be configured when setting up a new environment, and an existing dev can assist you in populating them.

[Composer](https://getcomposer.org/) will need to be run (`composer update`, `composer install`) when setting up a new environment. Composer is the PHP package manager used in this project.

The list of routes and what controller functions they map to are located in `routes/web.php`, or `routes/api.php` for API endpoints. These files are a good place to start for learning how to navigate the project for the first time.

Models are located in the `app` directory.

Controllers are located in the `app/Http/Controllers` directory.

Views are located in the `resources/views` directory.

Helper functions are loaded in from `app/helpers.php` and accessible almost anywhere when writing PHP.

Similarly, there are Javascript helper functions stored in `public/js/helpers.js`.

The 'master' template for all page views is `views/layoutes/app.blade.php`. All other pages use this template, populating the 'content' section at the very least.

[Node Package Manager](https://www.npmjs.com/) AKA 'npm' is used for the Javascript package management in this project. (run `sudo npm install --global cross-env` if you have an error about `cross-env` not being found when you run one of the npm scripts)

Javascript and CSS goes through a preprocessor/transpiler before being used in production. These files are stored in `public/js/processed` and `public/css/processed` (though this may change if someone implements a more dynamic system). These **are not** currently (2020-08-12) generated server-side, but rather need to be generated by the developer before merging into master. Generate them with `npm run production` from the root folder.

The [restcord](https://packagist.org/packages/restcord/restcord) package is being used for accessing Discord's API. As of 2021-09-21, we are using the `dev-develop` branch of restcord and not a proper version number. This is because only [dev-develop supports guzzlehttp ^7.0](https://githubmemory.com/repo/restcord/restcord/issues/156), which is a requirement for upgrading to Laravel 8.0.

## Caching

Redis has been chosen for caching. The `CACHE_DRIVER` environment variable will need to be configured appropriately for this to be taken advantage of.

You should install and use the [phpredis](https://github.com/phpredis/phpredis#installation) PHP extension. Alternatively (say; in a dev environment) `predis/predis` can be added with Composer. It is written in PHP, and it is far slower thhan `phpredis`, which is written in C.

The [genealabs/laravel-model-caching](https://packagist.org/packages/genealabs/laravel-model-caching) package is also in use. Its implementation is primarily seen at the top of classes with `use Cachable;`. This caches eloquent queries, handles automatic cache invalidation, and caches models.

### Cache Busting

Some pages allow for certain elements to be cache busted. To bust the cache, pass the `b=1` parameter in the url. The `SeeUser` middleware will check for this, and set a variable for cache busting to true. Certain controller elements will check for this variable before loading from the cache. If it's set to true, it will bust the cache on that **specific** bit that it wants to fetch before fetching it.

This feature isn't 100% implemented everywhere, so the cryptic name `b` has been opted for. Otherwise some users would see `bust` or `bustCache`, and expect everything to get busted all the time. They may then become confused or annoyed when it doesn't work.

## Translations

Translation files are exported using [kkomelin/laravel-translatable-string-exporter](https://github.com/kkomelin/laravel-translatable-string-exporter).

- Translation strings are stored in `resources/lang/`
- The list of supported translations are in `app/helpers.php:getLocales()`
- Use `php artisan translatable:inspect de` (replace `de` with whatever language code you want) to see what strings in the translation file still need to be translated.
- Use `php artisan translatable:export cn,da,de,en,es,fr,it,ko,no,pl,pt,ru` to update the translation files with all of the strings that the parser can find.
- For getting people to translate stuff, I've just been putting the translation files up in Google Sheets and giving people edit access, then importing it back into the repo (some parsing required).

## Warcraft Logs

Guilds may connect a Warcraft Logs account to their guild. They may also specify a Warcraft Logs Guild ID to associate with their TMB guild.

Within the code in this repo "Warcraft Logs" is written as one word: `warcraftlogs`, `Warcraftlogs`, or `WARCRAFTLOGS` depending on the context.

### PHP 7.4 > 8.0 Upgrade Notes

This project was originally written in an early version of PHP 7.4. To upgrade to 8.0, I performed the following (you may need to install additional packages `php-*` and change the nginx configuration based on your environment, or if you are using Apache instead of nginx):

```
sudo apt update
sudo apt upgrade
sudo add-apt-repository ppa:ondrej/php
sudo add-apt-repository ppa:ondrej/nginx
sudo apt update
sudo apt upgrade
sudo apt install php-curl
sudo apt install php-fpm
sudo apt install php-mysql
sudo apt install php-mbstring
sudo systemctl status php8.0-fpm
sudo a2enmod actions fcgid alias proxy_fcgi
sudo nano /etc/nginx/sites-available/thatsmybis.local
    > fastcgi_pass unix:/var/run/php/php8.0-fpm.sock
sudo systemctl restart nginx
```

You may need to copy over some custom configs to php8.0-fpm (`/etc/php/8.0/fpm/php.ini`), such as `max_input_vars` as defined elsewhere in this readme.

### phpredis Installation

#### Option 1

1. `apt install php-redis`
2. `apt install redis-server`

I required `phpize`, so I ran `sudo apt install php8.2-dev` (adjust for you version of php).

You may need to run `sudo apt install igbinary`.

You may need to add the following to your `php.ini` or `php-fpm.ini`:

```
extension=redis.so
extension=igbinary.so
```

#### Option 2

This worked on my local environment, but not on the production server:

1. `pecl install redis`
2. `enable igbinary serializer support?` > yes (it uses far less memory)
3. `enable lzf serializer support?` > no (I have no idea what this does)
4. `enable zstd serializer support?` > no (I have no idea what this does)

I required `igbinary`, so I ran `sudo pecl install igbinary`.

After installation, run `redis-server` to test that you can run an instance of the server.

Run `sudo systemctl enable redis-server` to tell the server to boot on system startup. If you're getting a `connection refused` error in the app, it may be because the Redis server isn't running.

## Local Environment Setup

The easiest way to get a local envrionment setup is with Laravel's configuration for Vagrant. Vagrant is a tool that makes it relatively painless to spin up a virtual machine containing a standardized dev environment for a specific project. This means that rather than configuring your operating system to have the appropriate packages, webserver, databases, and other requirements for this project, you just download and boot up a virtual machine that already has all that crap set up. This allows many developers to run the same dev environment, reducing troubleshooting and headaches, and putting more focus on the project itself.

Now, there's still going to be a little bit of work and learning curve involved in getting that VM setup. But believe me, it's better than the alternative.

Laravel has a custom environment for Vagrant. They call their environment 'Homestead', and that's what we're going to be setting up. It's easiest to get this up and running on Linux, but it's not much more work to get it running on Windows.

Best place to start? [RTFM](https://www.urbandictionary.com/define.php?term=RTFM): https://laravel.com/docs/8.x/homestead

tl;dr

- Download and install VirtualBox: https://www.virtualbox.org/wiki/Downloads
- Download and install Vagrant: https://www.vagrantup.com/downloads.html
- Download/clone the laravel/homestead repo, run `bash init.sh` (or `init.bat` in Windows), configure `Homestead.yaml`
- Generate .ssh keys or use the ones you have. (In Windows: `ssh-keygen -t rsa -C "your_email@example.com"`)
- A Windows configuration for `Homestead.yaml` looks like this:

  ```
  ---
  ip: "192.168.10.10"
  memory: 2048
  cpus: 2
  provider: virtualbox

  authorize: C:\Users\your_username\.ssh\id_rsa.pub

  keys:
      - C:\Users\your_username\.ssh\id_rsa

  folders:
      - map: C:\projects\thatsmybis
        to: /home/vagrant/code/thatsmybis

  sites:
      - map: thatsmybis.local
        to: /home/vagrant/code/thatsmybis/public

  databases:
      - thatsmybis

  features:
      - mariadb: false
      - ohmyzsh: false
      - webdriver: false

  # ports:
  #     - send: 50000
  #       to: 5000
  #     - send: 7777
  #       to: 777
  #       protocol: udp
  ```

- Make sure the hosts have been setup (read Hostname Resolution in the [docs](https://laravel.com/docs/7.x/homestead#configuring-homestead))
- Run `vagrant up` while in the Homestead directory.
- Run `vagrant ssh`
- Run `composer install` from `/home/vagrant/code/thatsmybis`
- Create a file named `.env` in `/home/vagrant/code/thatsmybis`. Base it off of `.env.example` or ask another dev for what details to fill in.

### Getting the `The server requested authentication method unknown to the client` error after spinning up Homestead?

Try running this in MySQL on your local environment: `ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'secret';`

## Items Table

The items table must be populated manually once your database is up and running. (create the database by running `php artisan migrate` once your environment is ready and you've already created your the empty database in mysql)

Find these insert statements in the DB repo: https://github.com/thatsmybis/classic-wow-item-db/tree/master/thatsmybis

Run the raw SQL from these files in mysql (in this order):

1. Insert `items`.
2. Insert `instances`.
3. Insert `item_sources`.
4. Insert `item_item_sources`.

## Permissions

kodeine\acl library was used for the permissions.

**NOTEWORTHY:** That library expected the User model to be the one with the roles and permissions. We did not use the User model. We used the Member model. Because of that, some references to 'user' or 'user_id' might not make any sense whatsoever. Have fun with that. I sure did. /s

**ALSO:** There's an override. The CheckGuildPermissions middleware will flag someone as a SUPER ADMIN if they're the guild's owner in the DB or (if I implement it), if they're one of the devs. the `hasPermissions()` function in the Member model will allow this flag to override the normal permissions. Science!

## Roles

Roles are loaded from the Discord server.

## Custom Configurations

- `ONLY_FULL_GROUP_BY` for SQL has been disabled in `database.php` by changing `strict` to `false`. This is to allow for writing simpler `GROUP BY` clauses in queries. If you can fix the `group by` complications caused by `strict`, you're welcome to turn it back on. I tried. It required mutilating my `SELECT` statements, and even then I couldn't get it to 100% work the way it did before 5.7 when it just assumed `ANY_VALUE()` on non-aggregated columns (even when I told it to use `ANY_VALUE()`). Good luck. ([SO thread](https://stackoverflow.com/questions/34115174/error-related-to-only-full-group-by-when-executing-a-query-in-mysql))
- `max_input_vars` in `php.ini` (PHP's config) has been increased from 1000 to 6000. This is to support some pages with an absurd amount of inputs. (ie. 120 items with 20 input fields each = 2400 inputs)

# Docker

## Docker Compose Local Development Environment

_Note: Docker was added by a contributor and is not maintained by the primary developer. Its config is probably out of date._

The laravel development environment can be bootstrapped by utilizing the bitnami laravel docker images to stand up laravel and mariadb.

Requirements: Docker Desktop

In the root of the project directory, there is a file called `docker-compose.yml`. This file contains the configuration for standing up the development environment. When the image starts, it is mapping in the the directory and sub directories of where it is located.

Run this in the root of the project to start the environment:

```
docker-compose up
```

This will run through and restore all of the project dependencies, start mariadb and the application and run the migration scripts.

Once Complete the following line will be printed:

```
 Laravel development server started: http://0.0.0.0:3000
```

This is a bit misleading as we have modified the docker-compose file to use port 80 instead of port 3000. Just remove the port and you should be able to load the page.

There is still a requirement to insert all of the items, instances, item_sources and item_item_sources from the db project. See section `Items Table` above.

Note: You can insert the items table by doing two things.

- You can connect to the container through docker and run commands inside of it.
  Example: `docker exec -it <container-id> bash`

- You can download a database management suite and connect to it to manage it. Example: MySQL Workbench, Adminer etc.. The connection endpoint will be `localhost:3306`

In order to see what containers you have running you can run `docker container ls`

Example Output:

```
CONTAINER ID        IMAGE                            COMMAND                  CREATED             STATUS              PORTS                    NAMES
56196af43aea        bitnami/laravel:7-debian-10      "/app-entrypoint.sh …"   26 hours ago        Up 5 minutes        0.0.0.0:80->3000/tcp     thatsmybis_thatsmybis_1
463541b65e80        bitnami/mariadb:10.1-debian-10   "/opt/bitnami/script…"   2 days ago          Up 5 minutes        0.0.0.0:3306->3306/tcp   thatsmybis_mariadb_1
```

## Docker Development with Visual Studio Code

A quick an dirty way to develop is to use Visual Studio Code with the PHP plugin which does intelisense and syntax highlighting. The laravel app will most times pick up the changes immediately if you refresh the page.

## Docker Development Laravel Development Commands

Commands can be launched inside the `thatsmybis` Laravel Development Container with `docker-compose` using the [exec](https://docs.docker.com/compose/reference/exec/) command.

> **Note**:
>
> The `exec` command was added to `docker-compose` in release [1.7.0](https://github.com/docker/compose/blob/master/CHANGELOG.md#170-2016-04-13). Please ensure that you're using `docker-compose` version `1.7.0` or higher.

The general structure of the `exec` command is:

```console
$ docker-compose exec <service> <command>
```

, where `<service>` is the name of the container service as described in the `docker-compose.yml` file and `<command>` is the command you want to launch inside the service.

Following are a few examples of launching some commonly used Laravel development commands inside the `thatsmybis` service container.

- List all `artisan` commands:

  ```console
  $ docker-compose exec thatsmybis php artisan list
  ```

- List all registered routes:

  ```console
  $ docker-compose exec thatsmybis php artisan route:list
  ```

- Create a new application controller named `UserController`:

  ```console
  $ docker-compose exec thatsmybis php artisan make:controller UserController
  ```

- Installing a new composer package called `phpmailer/phpmailer` with version `5.2.*`:

  ```console
  $ docker-compose exec thatsmybis composer require phpmailer/phpmailer:5.2.*
  ```
