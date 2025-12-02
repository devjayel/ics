# ICS Server — REST API Development

This repository contains the ICS server and REST API development project.

## Prerequisites

Before you begin, make sure you have the following installed:

- [Visual Studio Code](https://code.visualstudio.com/) — recommended editor.
- [XAMPP](https://www.apachefriends.org/index.html) — Apache, MySQL, PHP stack for local development.
- [Composer](https://getcomposer.org/) — PHP dependency manager.
- [Node.js](https://nodejs.org/) — JavaScript runtime (includes `npm`).

## Attachments

See the attached `readme.md` for basic project info and the repository files.

If you'd like, I can add quick setup commands for Windows (bash) to install or verify these prerequisites, or commit this change for you.

## Setup (quick)

Clone the repository and prepare the application (use your repo URL in place of `<repository-url>`). Run the commands one at a time:

- Clone the repository:

```bash
git clone <repository-url>
```

- Change into the project folder:

```bash
cd ics
```

- Install PHP dependencies with Composer:

```bash
composer install
```

- Copy the example environment file:

```bash
cp .env.example .env
```

- Generate the application key:

```bash
php artisan key:generate
```

- Run database migrations and seeders:

```bash
php artisan migrate --seed
```

- Create the storage symlink:

```bash
php artisan storage:link
```

- Build assets / serve the app locally:

```bash
composer run dev
```

Notes:
- If the project uses Node tooling directly, run `npm install` and `npm run dev` instead of `composer run dev`.
- Ensure your local database is configured in the `.env` file before running migrations.

