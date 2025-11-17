# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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

[Unreleased]: https://github.com/brammo/auth/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/brammo/auth/releases/tag/v1.0.0
