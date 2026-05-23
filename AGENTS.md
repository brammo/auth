# Agent instructions — Brammo/Auth

CakePHP 5 plugin (`brammo/auth`) providing session, form, and cookie authentication with login/logout, user status (`active` / `new` / `blocked`), and configurable routes and messages.

## Stack

- PHP **8.2+**, CakePHP **5.3+**, `cakephp/authentication` **3.x**
- Namespace: `Brammo\Auth\`
- Package type: `cakephp-plugin` (not a standalone app)

## Layout

| Path | Role |
|------|------|
| `src/AuthPlugin.php` | Bootstrap: loads `config/auth.php`, registers login/logout routes |
| `src/AuthenticationServiceProvider.php` | Builds `AuthenticationService` (Session, Form, Cookie) |
| `src/Controller/UserController.php` | `login()` / `logout()`, flash errors, optional password rehash |
| `src/Model/Entity/User.php` | Status constants, password mutator, `isActive()` / `isBlocked()` / `isStatusNew()` |
| `src/Model/Table/UsersTable.php` | Validation, `findActive()` |
| `config/auth.php` | Default `Auth.*` config; merged with app overrides via `Hash::merge` |
| `config/Migrations/` | `users` table schema |
| `templates/User/login.php` | Bootstrap login UI; domain `brammo_auth` for strings |
| `resources/locales/` | Plugin PO files (e.g. `bg/brammo_auth.po`) |
| `tests/test_app/` | Minimal host app for integration tests |
| `tests/TestCase/` | PHPUnit tests; fixtures under `tests/Fixture/` |

## Configuration (`Auth.*`)

Hosts override via `config/auth.php` or `Configure::write('Auth', ...)`.

- `Auth.Users.table` — ORM table class (default `Brammo/Auth.Users`)
- `Auth.Users.controller` — route target (default `Brammo/Auth.User`)
- `Auth.Routes.login` / `logout` / `loginRedirect`
- `Auth.Authentication.fields` — `username` → DB column (default `email`)
- `Auth.Authentication.finder` — default `active` (only active users authenticate)
- `Auth.Authentication.passwordHasher`, `rehashPasswords`, `sessionKey`, `cookieName`
- `Auth.Templates.login`, `Auth.Messages.*` (including `enumerateAccounts`)
- `Auth.Authentication.rememberMeField` — login checkbox for cookie persistence

Plugin config loads **last** and merges into existing `Auth` keys.

## Conventions

1. **`declare(strict_types=1);`** on all PHP files.
2. **CakePHP codesniffer** — run `composer cs-check` / `cs-fix`; PSR-12 + Cake rules (`phpcs.xml`).
3. **Static analysis** — `composer stan` (PHPStan 2 + cakedc/cakephp-phpstan), `composer psalm`; fix issues, do not baseline without reason.
4. **Tests** — PHPUnit 10; integration tests use `IntegrationTestTrait`, fixture `plugin.Brammo/Auth.Users`, SQLite in `tests/bootstrap.php`.
5. **New behavior** — add tests in matching `tests/TestCase/` tree; mirror existing setup (`Configure::write` in `setUp`, `Configure::clear` in `tearDown`).
6. **User-facing strings in plugin templates** — use `__d('brammo_auth', '...')` and update PO under `resources/locales/`.
7. **Flash messages in `UserController`** — use `__()` with keys from `Auth.Messages` (host app domain).
8. **Password hashing** — entity `_setPassword` uses `DefaultPasswordHasher`; authentication uses configured hasher in `AuthenticationServiceProvider`.
9. **Security** — do not log passwords; keep `password` in entity `$_hidden`; document that `showLoginError()` can leak account state (blocked vs new) when email exists.

## Quality gates (run before finishing)

```bash
composer check      # phpunit + phpcs
composer analyse    # phpstan + psalm
```

All **66** tests should pass. Treat PHPStan/Psalm failures as blockers unless already tracked in project docs.

## Integration in host apps

1. `composer require brammo/auth`
2. `$this->addPlugin('Brammo/Auth');`
3. `AuthenticationMiddleware` with `Brammo\Auth\AuthenticationServiceProvider` (or delegate from `Application`)
4. `bin/cake migrations migrate -p Brammo/Auth` (optional seed)

Extend `Brammo\Auth\Controller\UserController` in the host; allow unauthenticated `login` in `beforeFilter`.

## Common tasks

| Task | Where to change |
|------|-----------------|
| Login UX | `templates/User/login.php`, `templates/layout/login.php` |
| Auth identifiers / cookies | `AuthenticationServiceProvider.php`, `config/auth.php` |
| Status / finders | `User.php`, `UsersTable::findActive`, `config/auth.php` `finder` |
| Routes | `AuthPlugin::routes`, `Auth.Routes.*` |
| DB schema | `config/Migrations/`, keep `tests/bootstrap.php` + `UsersFixture` in sync |

## Do not

- Bump plugin version in `composer.json` unless releasing.
- Commit secrets, real salts, or `.env` files.
- Use `@phpstan-ignore` / widen types only to silence analysis.
- Break backward compatibility of `Auth.*` keys without changelog + README.
- Require `name` in validation without matching migration/README expectations.

## References

- README.md — install, config examples, troubleshooting
- CHANGELOG.md — version history
- `docs/PHPSTAN.md`, `docs/PSALM.md` — analyser setup
