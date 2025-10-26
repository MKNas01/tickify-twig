# Tickify - Ticket Management App (Twig/PHP Version)

A robust ticket management web app built for HNG-13 Stage 2 using Twig/PHP (server-side). Features landing page, auth (login/signup with validation), dashboard (stats/logout), and CRUD tickets (server-side val, session persistence). Identical UI across frameworks (wave hero, circles, 1440px max, responsive).

## Frameworks & Libraries Used
- **Twig 3**: Templating engine for UI.
- **PHP 8+**: Core server-side logic (sessions for auth/tickets).
- **Vanilla CSS**: Responsive (Flex/Grid, media queries), semantic HTML, accessibility (ARIA, focus).
- **Apache/PHP built-in server**: Routing/hosting.

## Setup & Run Locally
1. Clone repo: `git clone https://github.com/MKNas01/tickify-twig && cd tickify-twig`.
2. Install deps: `composer install`.
3. Run server: `php -S localhost:8000`.
4. Visit `localhost:8000` → Test flow.

## Switching Versions
- **React**: [GitHub](https://github.com/MKNas01/tickify-react) | [Netlify Live](https://tickify-react.netlify.app/tickets).
- **Vue**: [GitHub](https://github.com/MKNas01/tickify-vue) | [Netlify Live](https://tickify-vue.netlify.app/).
- **Twig**: [GitHub](https://github.com/MKNas01/tickify-twig) | [Render Live](https://tickify-twig.onrender.com/auth/login).

## UI Components & State Structure
- **Landing**: Static hero (wave SVG, circles, CTAs), features cards, footer.
- **Auth (Login/Signup)**: Forms with PHP POST val, inline errors, password toggle (JS).
- **Dashboard**: Stats cards (PHP computed from session), sidebar nav (links), logout clears session.
- **Tickets**: Form (PHP POST for CRUD), list (Twig for loop with status classes), delete confirm (JS).
- **State**: PHP $_SESSION for user/tickets (server-side sim localStorage).

## Accessibility & Notes
- Semantic HTML (`<main>`, `<section>`, `<label for>`, ARIA-describedby for errors).
- Focus visible (`:focus { outline: 2px solid #60A5FA; }`), keyboard nav (buttons/links).
- Responsive: Mobile stack/column, desktop row/grid (900px+ breakpoint).
- Notes: Sessions for persistence (server-side); test in incognito. No JS libs—vanilla PHP/Twig. No known issues—Lighthouse accessibility 95%+.

## Test User Credentials
- Signup any email (e.g., test@example.com), password >=6 chars.
- Login with created creds.
- Create tickets: Title required, status dropdown, desc optional (>=10 chars if filled).

Built for HNG-13 Frontend Track. Questions? Contact nasirumuhammedkabirux@gmail.com .