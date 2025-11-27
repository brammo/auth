# Brammo/Auth Tests

This directory contains the test suite for the Brammo/Auth plugin.

## Test Structure

```
tests/
├── bootstrap.php                    # Test environment bootstrap
├── Fixture/                         # Test fixtures for database data
│   └── UsersFixture.php            # Users table fixture
├── test_app/                       # Test application setup
│   ├── TestApp/
│   │   └── Application.php         # Test application class
│   └── config/
│       ├── app.php                 # Test app configuration
│       └── routes.php              # Test routes
└── TestCase/                       # Test cases
    ├── PluginTest.php              # Tests for Plugin class
    ├── AuthenticationServiceProviderTest.php  # Tests for AuthenticationServiceProvider
    ├── Controller/
    │   └── UserControllerTest.php  # Tests for UserController
    ├── Model/
    │   ├── Entity/
    │   │   └── UserTest.php        # Tests for User entity
    │   └── Table/
    │       └── UsersTableTest.php  # Tests for UsersTable
```

## Running Tests

Run the complete test suite:

```bash
composer test
```

Run code sniffer to check code style:

```bash
composer cs-check
```

Fix code style issues:

```bash
composer cs-fix
```

## Test Coverage

The test suite covers:

### Plugin Class (`PluginTest.php`)
- Plugin bootstrap process
- Configuration loading
- Route registration
- Custom route configuration

### AuthenticationServiceProvider (`AuthenticationServiceProviderTest.php`)
- Authentication service creation
- Service configuration
- Authenticator setup
- Custom configuration handling

### UserController (`UserControllerTest.php`)
- Login page display
- Valid credential authentication
- Invalid credential handling
- Redirect functionality
- Logout process
- Unauthenticated access
- Empty data validation
- Login with active user status
- Login attempt with blocked user (shows blocked message)
- Login attempt with new/inactive user (shows activation message)
- Custom error messages from configuration

### User Entity (`UserTest.php`)
- Field accessibility
- Hidden field handling (password)
- JSON serialization
- Mass assignment protection
- Property access
- Password hashing on set
- Empty password handling
- Password hash uniqueness (salt verification)
- Special character password support
- Unicode character password support
- Status field accessibility
- `isActive()` method behavior
- `isBlocked()` method behavior
- Status constants (STATUS_ACTIVE, STATUS_NEW, STATUS_BLOCKED)

### UsersTable (`UsersTableTest.php`)
- Table initialization
- Validation rules
- Email validation
- Email uniqueness constraint
- Password requirements
- CRUD operations
- Timestamp behavior
- Field length validation
- `findActive()` finder returns only active users
- `findActive()` excludes blocked users
- `findActive()` excludes new/inactive users
- Status validation with valid values
- Status validation with invalid values
- Status default value

## Note on Fixture Setup

The tests use a file-based SQLite database for reliable test execution. The database schema is created in `bootstrap.php` using CakePHP's `TableSchema` class with the same field definitions as the fixtures.

**Why this approach?**
- Single source of truth: Schema is defined once using CakePHP's schema format
- Cross-database compatibility: Uses CakePHP's schema abstraction instead of raw SQL
- Fixture alignment: Schema matches `UsersFixture::$fields` exactly

The `UsersFixture` provides test data with:
- Test user (email: test@example.com, password: password, status: active)
- Admin user (email: admin@example.com, password: password, status: active)
- Blocked user (email: blocked@example.com, password: password, status: blocked)
- New user (email: new@example.com, password: password, status: new)

## Requirements

- PHP 8.1+
- PHPUnit 10+
- CakePHP 5.0+
- CakePHP Authentication 3.0+

## Continuous Integration

This test suite is designed to run in CI environments. Configure your CI pipeline to:

1. Install dependencies: `composer install`
2. Run tests: `composer test`
3. Check code style: `composer cs-check`
