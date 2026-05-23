# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.2.0] - 2026-05-23

### Added
- `Auth.Messages.enumerateAccounts` config to disable status-specific login error messages
- `Auth.Authentication.rememberMeField` config for cookie "remember me" checkbox (default: `remember_me`)
- `Auth.Authentication.sessionKey` config option to customize the session key (default: `Auth`)
- `Auth.Authentication.cookieName` config option to customize the remember-me cookie name (default: `CookieAuth`)
- `Auth.Authentication.rehashPasswords` config option to control password rehashing on login
- `User::isStatusNew()` status helper (not `isNew()` — conflicts with `Entity::isNew()`)
- Bulgarian locale file for plugin template domain `brammo_auth`
- `docs/I18N.md` with translation extraction instructions

### Changed
- Login template strings use `__d('brammo_auth', …)` domain
- `name` field is optional on create (matches database schema)
- Entity password hashing uses `Auth.Authentication.passwordHasher` configuration
- Password rehashing disabled by default (`Auth.Authentication.rehashPasswords` defaults to `false`)
- Minimum PHP version 8.2 (was 8.1)
- Minimum CakePHP version 5.3 (was 5.0)
- Minimum `cakephp/migrations` version 5.0 (was 4.0)

### Fixed
- Duplicate flash render call on login template
- PHPStan level 8 compliance for `UsersTable` PHPDoc
- PHPStan and Psalm errors

## [1.1.0] - 2025-11-27

### Added
- User status field with three values: 'active', 'new', 'blocked'
- Default status is 'new' for newly created users
- `findActive()` finder method on UsersTable to query only active users
- Configurable authentication finder via `Auth.Authentication.finder` config
- Configurable error messages for authentication failures:
  - `Auth.Messages.invalidCredentials` - Invalid credentials message
  - `Auth.Messages.blocked` - Message for blocked users
  - `Auth.Messages.notActivated` - Message for inactive/new users
- Status helper methods on User entity:
  - `isActive()` - Check if user status is 'active'
  - `isBlocked()` - Check if user status is 'blocked'
- Status constants on User entity:
  - `User::STATUS_ACTIVE` = 'active'
  - `User::STATUS_NEW` = 'new'
  - `User::STATUS_BLOCKED` = 'blocked'
- Status validation in UsersTable (must be one of: active, new, blocked)
- Migration `20251127000000_AddStatusToUsers` to add status column
- Status field in UsersFixture with test users for each status
- Comprehensive tests for all new status functionality

### Changed
- UserController now shows status-specific error messages on login failure
- AuthenticationServiceProvider uses configurable finder (default: 'all')
- Updated UsersSeed with status field

## [1.0.0] - 2025-11-17

### Added
- Initial release of Brammo/Auth plugin for CakePHP 5.x
- Complete authentication system with login/logout functionality
- User authentication using CakePHP Authentication library
- Configurable authentication routes (`/login`, `/logout`)
- Flexible configuration system via `config/auth.php`
- Support for multiple authentication methods:
  - Session-based authentication
  - Form-based authentication
  - Cookie-based authentication (Remember Me)
- Password hashing with bcrypt (default)
- Support for legacy password hashers (fallback configuration)
- Password rehashing on login when needed
- Custom login redirect support (query parameter or configuration)
- Plugin bootstrap with automatic configuration loading
- User entity with:
  - Hidden password field in JSON output
  - Mass-assignable fields (email, password, name)
  - Proper field accessibility
- UsersTable with:
  - Email validation
  - Unique email constraint
  - Timestamp behavior
  - Validation rules for name, email, password
- UserController with:
  - Login action with redirect support
  - Logout action
  - Flash messages for authentication errors
  - Unauthenticated access control
- AuthenticationServiceProvider for service configuration
- Database migrations:
  - CreateUsers migration (20231101000000)
  - Users table with id, name, email, password, created, modified
  - Unique index on email field
- Database seeds:
  - UsersSeed with sample admin and test users
- Comprehensive test suite with 37+ test cases:
  - PluginTest - Plugin bootstrap and routes
  - AuthenticationServiceProviderTest - Service configuration
  - UserControllerTest - Login/logout functionality
  - UserTest - Entity behavior
  - UsersTableTest - Table operations and validation
- Full PHPUnit configuration
- Test fixtures for Users table
- Test application structure for integration testing
- Code style checking with CakePHP CodeSniffer
- Comprehensive documentation:
  - Installation and setup guide
  - Configuration examples
  - Usage examples
  - API documentation
  - Troubleshooting guide
  - Migration documentation

### Dependencies
- PHP 8.1 or higher
- CakePHP 5.0 or higher
- CakePHP Authentication 3.0 or higher
- CakePHP Migrations 4.0 or higher

### Development Dependencies
- PHPUnit 10.0 or higher
- CakePHP CodeSniffer 5.0 or higher

## Release Notes

### 1.0.0 - First Stable Release

This is the first stable release of the Brammo/Auth plugin. It provides a complete, 
production-ready authentication system for CakePHP 5 applications.

**Key Features:**
- Drop-in authentication solution
- Minimal configuration required
- Secure password handling with bcrypt
- Flexible and extensible architecture
- Well-tested codebase
- Comprehensive documentation

**Installation:**
```bash
composer require brammo/auth
```

**Quick Start:**
```bash
# Load the plugin
bin/cake plugin load Brammo/Auth

# Run migrations
bin/cake migrations migrate -p Brammo/Auth

# Optional: Seed with sample users
bin/cake migrations seed -p Brammo/Auth --seed UsersSeed
```

[Unreleased]: https://github.com/brammo/auth/compare/v1.2.0...HEAD
[1.2.0]: https://github.com/brammo/auth/compare/v1.1.0...v1.2.0
[1.1.0]: https://github.com/brammo/auth/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/brammo/auth/releases/tag/v1.0.0
