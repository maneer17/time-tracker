# Time Tracker

Log what you work on each day, see where your time goes, and share your days with a team. English and Arabic (RTL).

**Live:** https://timetracker.manar.page/ (deployed on Oracle Cloud)

## What it does

- **Time log:** add entries with a label and start/end time, search, filter by day, edit, delete.
- **Reports:** totals, time per label, charts, and a PDF export.
- **Channels:** create channels, invite people by email or username, share a day with a channel and discuss it in comments.
- **Notifications:** in the app (real time) and by email, with per-type email settings.
- **Exports:** time entries, a shared day or a whole channel as CSV, Excel or PDF.
- **Accounts:** email and password or Google sign-in, with password reset.

## Built with

Laravel 10 (Sanctum, Reverb, queues) · Vue 3 (Vite, Pinia, Tailwind, vue-i18n, Chart.js) · MySQL · Redis · Docker + Nginx

## Run it

```bash
git clone git@github.com:maneer17/time-tracker.git
cd time-tracker
cp backend/.env.example backend/.env
docker compose up --build
```

Open http://localhost:8080 and create your own account. The first start takes a few minutes while it installs and sets up the database.

Without Docker: run `php artisan serve`, `php artisan queue:work` and `php artisan reverb:start` in `backend/`, and `npm install && npm run dev` in `frontend/` (Node 20.19+).

Optional settings live in `backend/.env`: `MAIL_*` for real email (the default only logs it) and `CLIENT_ID` / `CLIENT_SECRET` for Google sign-in.

## Tests

```bash
docker compose exec backend php artisan test
```

They run on an in-memory SQLite database and never touch your data.

## Project layout

```
backend/    Laravel API: app/ (controllers, models, policies, notifications), routes/, tests/
frontend/   Vue app: src/ (views, components, stores, services, locales)
docker/     Nginx and scheduler config
```
