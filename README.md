# Tickify - Ticket Management App (Twig/PHP Version)

Server-side version with PHP/Twig for HNG-13 Stage 2. Mirrors React/Vue UI (landing, auth, dashboard, CRUD tickets) using sessions for persistence.

## Setup & Run Locally
1. Clone: `git clone [REPO_URL] && cd tickify-twig`.
2. Install deps: `composer install`.
3. Run server: `php -S localhost:8000`.
4. Visit `localhost:8000` → Test flow.

## Notes
- Sessions for auth/tickets (server-side sim localStorage).
- No JS libs—vanilla PHP/Twig.
- Test creds: Signup any email/pass >=6 chars.

[Netlify/Render URL]: [Your Deploy URL]