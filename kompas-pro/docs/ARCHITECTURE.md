# Architecture

- **Auth**: PHP form login (`auth.php`) and `logout.php`; accounts in the `users` table (bcrypt via `password_hash`), session-based.
- **Database**: MySQL through PDO. Schema in `database.sql` (users, transactions, tasks, habits, habit_logs, notes). `src/bootstrap.php` creates the database and tables automatically on first run.
- **Data flow**: the dashboard keeps its state in memory. `sync.php` is a small bridge: GET loads the user's rows from MySQL, POST saves them (inside a transaction, with whitelisting and casting of every field).
- **Frontend**: vanilla JS in classic scripts loaded in order (`utils` → `seed` → `helpers` → `views` → `render` → `actions` → `main`). Every mutation calls `up()`, which saves (debounced) and re-renders.
- **Security note**: intentionally simple (no rate limiting or CSRF tokens). `src/` is denied via `.htaccess` because `config.php` holds DB credentials.
- **Next step**: per-item endpoints instead of replacing a user's rows on each save.
