# Time Tracker: frontend

Vue 3 single-page app for the Time Tracker API. See the [root README](../README.md) for the full project, architecture and the Docker quickstart.

## Stack

Vue 3, Vite, Pinia, Vue Router, vue-i18n (English and Arabic, with RTL), Tailwind CSS 4, Chart.js, Axios, Laravel Echo (real-time notifications through Reverb).

## Development

Requires Node 20.19+ or 22.12+ and the backend running (`docker compose up` from the repository root).

```bash
npm install
npm run dev      # http://localhost:5173
npm run build    # production build in dist/
npm run preview  # serve the production build locally
```

Set `FRONTEND_URL=http://localhost:5173` in `backend/.env` so links in emails and invitation redirects point at the dev server.

## Configuration

`VITE_*` variables are read at build time from `.env.development` and `.env.production`:

| Variable | Purpose |
| --- | --- |
| `VITE_API_BASE_URL` | Base URL of the API (the Nginx entry point) |
| `VITE_REVERB_APP_KEY`, `VITE_REVERB_HOST`, `VITE_REVERB_PORT`, `VITE_REVERB_SCHEME` | WebSocket connection; the key must match `REVERB_APP_KEY` in `backend/.env` |
| `VITE_CLIENT_ID` | Google OAuth client id (optional, for Google Sign-In) |

Because they are compiled in, rebuild the frontend after changing them (`docker compose up -d --build frontend`).

## Structure

```
src/
├── views/         # one component per route
├── components/    # grouped by feature: channel, time-entries, charts, notifications, ...
├── stores/        # Pinia: auth, notifications, comments
├── services/      # Axios wrappers, one per API resource (api.js holds the shared client)
├── composables/   # useApi, usePagination, useExport
├── locales/       # en.json, ar.json
└── router/        # routes and the auth guard
```

## Behaviour worth knowing

- The language is remembered in `localStorage` (first visit follows the browser language, falling back to English) and sets `<html lang>` and `dir`.
- A `401` response outside the login and registration forms ends the session and returns to the login page.
- Unknown URLs show a 404 page.

## Translation messages

- In a message, `@` starts a linked message, `|` separates plural forms and `{ }` is a placeholder. A bare `@` (an email placeholder, say) breaks the compiler and **blanks the whole page** in the browser, so write it as `{'@'}`: `"name{'@'}company.com"`. The test suite compiles every message with the real compiler to catch this.

## Arabic and RTL

- `<html lang>` and `dir` follow the language. Layout uses Tailwind's logical utilities (`ms-`, `pe-`, `text-start`, `end-0`, ...) and `rtl:` variants, so it mirrors automatically. Avoid `ml-`, `pr-`, `left-`, `text-right` and friends in new code.
- Dates, times, relative times and the am/pm marker go through `composables/useLocaleFormat.js` (Latin digits in both languages).
- Counted nouns use full Arabic plural forms: messages with six variants (`zero | one | two | few | many | other`) are resolved by the rule in `plugins/i18n.js`.
- Notification sentences are built in the browser from structured fields (`channel_name`, `excerpt`, ...) so they follow the language; notifications stored before those fields existed fall back to their English text.
- Error toasts are translated; unknown server errors (404/5xx) never expose internals.
- Charts mirror in Arabic (bars grow from the right, legends and tooltips read right-to-left).
