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

### User Entity (`UserTest.php`)
- Field accessibility
- Hidden field handling (password)
- JSON serialization
- Mass assignment protection
- Property access

### UsersTable (`UsersTableTest.php`)
- Table initialization
- Validation rules
- Email validation
- Email uniqueness constraint
- Password requirements
- CRUD operations
- Timestamp behavior
- Field length validation

## Note on Fixture Setup

The tests use an in-memory SQLite database for fast test execution. The `UsersFixture` provides test data with:
- Test user (email: test@example.com, password: password)
- Admin user (email: admin@example.com, password: admin123)

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
