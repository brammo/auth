# Copilot instructions — Brammo/Auth

This repository is a **CakePHP 5 authentication plugin**, not a full application. Read `AGENTS.md` for full context.

## Quick facts

- Namespace `Brammo\Auth\`, PHP 8.2+, CakePHP 5.3+
- Entry: `AuthPlugin`, `AuthenticationServiceProvider`, `UserController`
- Defaults in `config/auth.php` under `Auth.*`
- Tests: `composer test` (66 tests, SQLite test app in `tests/test_app/`)

## When editing

1. Match existing CakePHP plugin patterns and PHPDoc style.
2. Add/update PHPUnit tests for behavior changes.
3. Run `composer check` and `composer analyse`.
4. Plugin template strings: `__d('brammo_auth', 'Message')` + locale PO files.
5. Keep migrations, fixtures, and `tests/bootstrap.php` schema aligned.

## Avoid

- Breaking `Auth.*` configuration contracts without documenting in CHANGELOG/README.
- Suppressing PHPStan/Psalm with ignores instead of fixing types.
- Hardcoding routes or table names that are already configurable.
