# Kompas – Daily Control Center

A personal dashboard for finances, kanban tasks, habit streaks, and notes. PHP session login, MySQL storage, a vanilla JavaScript frontend, and Chart.js visualizations.

## Features
- **Accounts**: register, login, logout (hashed passwords in MySQL)
- **Overview**: monthly balance, 6-month income vs. expense chart, expense breakdown, upcoming deadlines
- **Finance**: income/expense log with month filter
- **Tasks**: 3-column kanban board with priorities and due dates
- **Habits**: 7-day tracker with streaks
- **Notes**: write, pin, search
- **Settings**: light/dark theme, JSON export/import, reset to sample data
- Responsive UI, each user's data stored in MySQL

## Tech Stack
PHP 8 (PDO) · MySQL · JavaScript · Chart.js · Docker · GitHub Actions

## Database
`users` · `transactions` · `tasks` · `habits` · `habit_logs` · `notes` (see `database.sql`). Tables are created automatically on first run.

## Getting Started
**Laragon / XAMPP**: copy the folder to `www` (or `htdocs`), start Apache and MySQL, open `http://localhost/kompas-pro/auth.php`. Default config is user `root` with an empty password; change it in `src/config.php`.

**Docker**: `docker compose up --build` → http://localhost:8080/auth.php

Run tests (needs MySQL): `php tests/smoke.php`

## Project Structure
```
├── index.php · auth.php · logout.php · sync.php
├── src/ (bootstrap.php, config.php)
├── database.sql
├── assets/ (css, js)
├── tests/smoke.php
├── docs/ARCHITECTURE.md
└── Dockerfile · docker-compose.yml · .github/workflows/ci.yml
```

## Roadmap
Per-item REST endpoints, login rate limiting, CSRF tokens, savings goals, reminders, PWA/offline mode.

## License
MIT
