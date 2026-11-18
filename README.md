# HKI2050 backend

The backend for HKI2050. It runs on [ProcessWire](https://processwire.com) 3.0.246, and the
[AppApi](https://github.com/Sebiworld/AppApi) module (v1.3.4) serves its REST API at `/api/`.

This repository contains only the project's own code. The ProcessWire core, the AppApi module,
the site config, the database and uploaded files are **not** committed. The steps below show
where to get each of them.

## What's in the repo

| Path | Contents |
|---|---|
| `site/api/` | API routes (`Routes.php`) and handlers for articles, characters and things |
| `site/migrations/` | One-off scripts that change the database schema (fields, templates) |
| `site/templates/` | ProcessWire template files |
| `.ddev/config.yaml` | Local development environment (PHP 8.2, nginx, MariaDB 10.11) |
| `.htaccess` | Production rewrite rules (Apache) |

## Before you start

You need:

- [DDEV](https://ddev.readthedocs.io/en/stable/users/install/) and a Docker provider
  (OrbStack, Docker Desktop, Colima…)
- Access to the live server, or someone who has it, for three things:
  1. `site/config.php`
  2. A database dump
  3. Uploaded files from `site/assets/files/` (optional; without them, images are missing)

The config and the database have to come from the same place. The password hashes in the
database only work with the `userAuthSalt` in that `config.php`.

## Installation

### 1. Clone the repository

```sh
git clone git@github.com:ninjapiraatti/hki-pw.git
cd hki-pw
```

### 2. Add the ProcessWire core

Use the same version as live (3.0.246):

```sh
git clone --depth 1 --branch 3.0.246 https://github.com/processwire/processwire.git /tmp/pw
cp -R /tmp/pw/wire /tmp/pw/index.php .
cp -R /tmp/pw/site-blank/classes /tmp/pw/site-blank/init.php /tmp/pw/site-blank/ready.php /tmp/pw/site-blank/finished.php site/
mkdir -p site/assets/cache site/assets/files site/assets/logs site/assets/sessions
rm -rf /tmp/pw
```

### 3. Add the AppApi module

```sh
git clone --depth 1 --branch v1.3.4 https://github.com/Sebiworld/AppApi.git site/modules/AppApi
rm -rf site/modules/AppApi/.git
```

Its PHP dependencies are already in the module's `vendor/` directory, so you don't need to run
`composer install`.

### 4. Configure ProcessWire

Put the live `config.php` in `site/config.php`. Then create `site/config-local.php`.
`config.php` loads it at the end, and its values override the live database and host settings:

```php
<?php namespace ProcessWire;
if(!defined("PROCESSWIRE")) die();

$config->dbHost = 'db';
$config->dbName = 'db';
$config->dbUser = 'db';
$config->dbPass = 'db';
$config->httpHosts = ['hki-pw.ddev.site'];
$config->debug = true;
```

Git ignores both files. Never commit them: `config.php` contains the live database password
and salts.

### 5. Start DDEV and import the database

```sh
ddev start
ddev import-db --file=path/to/dump.sql.gz   # .sql works too
```

### 6. Copy uploaded files (optional)

Copy the live `site/assets/files/` directory into `site/assets/files/`, for example:

```sh
rsync -av user@live-server:/path/to/site/assets/files/ site/assets/files/
```

### 7. Run the migrations

Run every script in `site/migrations/`. They are safe to run more than once:

```sh
ddev exec php site/migrations/add-character-draft-fields.php
```

### 8. Open the site

| | URL |
|---|---|
| Site | https://hki-pw.ddev.site/ |
| Admin | https://hki-pw.ddev.site/processwire/ |
| API | https://hki-pw.ddev.site/api/ |

The database is a copy of live, so you log in to the admin with your live credentials.

## The API

Routes are defined in `site/api/Routes.php`. Every endpoint requires authentication (`'auth' => true`).

| Resource | Endpoints |
|---|---|
| Articles | `GET/POST /api/articles`, `GET/PUT/DELETE /api/articles/{id}` |
| Characters | `GET/POST /api/characters`, `GET/PUT/DELETE /api/characters/{id}`, `PUT/DELETE /api/characters/{id}/draft` |
| Things | `GET/POST /api/things`, `GET/PUT/DELETE /api/things/{id}` |

AppApi decides which applications can call the API and how they authenticate. Manage this in
the admin under **Setup → AppApi**. Each request sends the application's API key in the
`X-API-KEY` header, and a user logs in through AppApi's `/api/auth` endpoint. See the
[AppApi documentation](https://github.com/Sebiworld/AppApi) for details.

To add an endpoint, add a handler class in `site/api/`, `require_once` it in `Routes.php`, and add
its routes to the `$routes` array.

## Frontend

The frontend is a separate application. In production its build output goes in `app/` (ignored
by git), and the root `.htaccess` sends every request that isn't for `/processwire/` or `/api/`
to `app/index.html`. Locally, `/` shows the ProcessWire home page instead, because DDEV runs
nginx and doesn't read `.htaccess`.

For local frontend development, `site/templates/home.php` allows CORS requests from
`http://localhost:5173`, the Vite dev server's default port.

## Changing the database schema

ProcessWire stores fields and templates in the database, not in files. Don't make schema
changes only in the admin, because the change then exists only in your database. Write a script
in `site/migrations/` that makes the change (see `add-character-draft-fields.php` for the
pattern), then run it locally and on live.

It's a good idea to take a snapshot before running one:

```sh
ddev snapshot --name=before-my-change
ddev snapshot restore before-my-change   # to roll back
```
