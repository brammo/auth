# Brammo/Auth

A comprehensive authentication plugin for CakePHP 5.x applications, providing user authentication, 
login/logout functionality, and configurable authentication services.

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![CakePHP](https://img.shields.io/badge/CakePHP-5.0-red.svg)](https://cakephp.org)
[![PHP Version](https://img.shields.io/badge/PHP-8.1+-blue.svg)](https://php.net)

## Features

- **Complete Authentication System** - Login/logout functionality out of the box
- **Highly Configurable** - Customize routes, templates, password hashers, and more
- **Secure** - Uses CakePHP's authentication library with bcrypt password hashing
- **Flexible** - Easy to integrate into existing applications

## Requirements

- PHP 8.1 or higher
- CakePHP 5.0 or higher
- CakePHP Authentication 3.0 or higher

## Installation

Install the plugin using Composer:

```bash
composer require brammo/auth
```

Load the plugin in your application's `src/Application.php`:

```php
public function bootstrap(): void
{
    parent::bootstrap();
    
    $this->addPlugin('Brammo/Auth');
}
```

## Configuration

### Basic Setup

The plugin comes with sensible defaults, but you can customize it by creating a configuration file 
at `config/auth.php` in your application:

```php
<?php
return [
    'Auth' => [
        'Users' => [
            'table' => 'Users',  // Your users table
            'controller' => 'Users',  // Your users controller
        ],
        'Routes' => [
            'login' => '/login',
            'logout' => '/logout',
            'loginRedirect' => '/',
        ],
        'Authentication' => [
            'fields' => [
                'username' => 'email',
                'password' => 'password',
            ],
            'passwordHasher' => [
                'className' => 'Authentication.Default',
            ],
        ],
        'Templates' => [
            'login' => 'Users/login',  // Your login template
        ],
    ],
];
```

### Authentication Middleware

Add the Authentication middleware to your application's middleware queue in `src/Application.php`:

```php
use Authentication\Middleware\AuthenticationMiddleware;
use Brammo\Auth\AuthenticationServiceProvider;

public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
{
    $middlewareQueue
        // ... other middleware
        ->add(new AuthenticationMiddleware(new AuthenticationServiceProvider()))

    return $middlewareQueue;
}
```

### Database Schema

The plugin includes migrations to create the users table. Run the migrations:

```bash
# Run migrations from the plugin
bin/cake migrations migrate -p Brammo/Auth

# Or seed with sample users (for development/testing)
bin/cake migrations seed -p Brammo/Auth --seed UsersSeed
```

The migration creates a users table with the following structure:
- `id` - Primary key
- `name` - User's display name (optional)
- `email` - Unique email address (required)
- `password` - Hashed password (required)
- `created` - Timestamp of creation
- `modified` - Timestamp of last modification

Sample users created by the seed:
- **Admin User**: email: `admin@example.com`, password: `admin123`
- **Test User**: email: `user@example.com`, password: `password`

Alternatively, create the table manually:

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created DATETIME,
    modified DATETIME
);
```

Or use CakePHP's bake command:

```bash
bin/cake bake migration CreateUsers name:string email:string:unique password:string created modified
bin/cake migrations migrate
```

## Usage

### Login Template

The plugin provides simple login form template. You can create a custom login template at 
`templates/Users/login.php` (or your configured path):

```php
<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="users form">
    <?= $this->Form->create() ?>
    <fieldset>
        <legend><?= __('Please enter your email and password') ?></legend>
        <?= $this->Form->control('email', ['required' => true]) ?>
        <?= $this->Form->control('password', ['required' => true]) ?>
    </fieldset>
    <?= $this->Form->submit(__('Login')); ?>
    <?= $this->Form->end() ?>
</div>
```

### Controller Usage

The plugin provides a `UserController` with login and logout actions. You can extend or override it:

```php
namespace App\Controller;

use Brammo\Auth\Controller\UserController as BrammoUserController;

class UsersController extends BrammoUserController
{
    // Override or extend as needed
}
```

### Protecting Routes

In your controllers, use the Authentication component to protect actions:

```php
public function beforeFilter(\Cake\Event\EventInterface $event)
{
    parent::beforeFilter($event);
    
    // Allow unauthenticated access to specific actions
    $this->Authentication->allowUnauthenticated(['index', 'view']);
}
```

### Checking Authentication

Check if a user is authenticated:

```php
$user = $this->Authentication->getIdentity();
if ($user) {
    // User is logged in
    $email = $user->email;
}
```

In templates:

```php
<?php if ($this->Identity->isLoggedIn()): ?>
    <p>Welcome, <?= h($this->Identity->get('name')) ?>!</p>
    <?= $this->Html->link('Logout', ['controller' => 'Users', 'action' => 'logout']) ?>
<?php else: ?>
    <?= $this->Html->link('Login', ['controller' => 'Users', 'action' => 'login']) ?>
<?php endif; ?>
```

## Advanced Configuration

### Custom Password Hashers

Use legacy password hashers or multiple hashers:

```php
'Authentication' => [
    'passwordHasher' => [
        'className' => 'Authentication.Fallback',
        'hashers' => [
            'Authentication.Default',
            [
                'className' => 'Authentication.Legacy',
                'hashType' => 'sha1',
            ],
        ],
    ],
],
```

### Custom Login Redirect

Redirect users to different locations after login:

```php
// In your login URL /login?redirect=/dashboard

// Or configure default in config/auth.php
'Routes' => [
    'loginRedirect' => '/dashboard',
],
```

### Cookie-Based Authentication

The plugin includes cookie-based authentication by default. Configure it in your Authentication service if needed.

## Testing

Run the test suite:

```bash
composer test
```

Run static analysis with PHPStan:

```bash
composer stan
```

Run static analysis with Psalm:

```bash
composer psalm
```

Run all static analysis tools:

```bash
composer analyse
```

Check code style:

```bash
composer cs-check
```

Fix code style issues:

```bash
composer cs-fix
```

Run all checks:

```bash
composer check
```

## API Documentation

### Plugin Class

The main plugin class handles:
- Loading plugin configuration
- Registering authentication routes
- Bootstrap process

### AuthenticationServiceProvider

Provides the authentication service with:
- Session authenticator
- Form authenticator
- Cookie authenticator
- Password identifier with ORM resolver

### UserController

Handles authentication actions:
- `login()` - Display login form and process authentication
- `logout()` - Log out user and redirect to login

### User Entity

Represents a user with:
- Hidden password field in JSON output
- Mass-assignable fields (email, password, name)

### UsersTable

Manages user data with:
- Email validation
- Unique email constraint
- Timestamp behavior
- Validation rules

## Troubleshooting

### Users Not Authenticating

1. Ensure password is hashed correctly:
```php
$user = $this->Users->newEntity([
    'email' => 'user@example.com',
    'password' => 'plaintext',  // Will be hashed automatically
]);
```

2. Check that Authentication middleware is loaded
3. Verify configuration matches your database schema

### Redirect Loop

Ensure login action is allowed for unauthenticated users:

```php
public function beforeFilter(\Cake\Event\EventInterface $event)
{
    parent::beforeFilter($event);
    $this->Authentication->allowUnauthenticated(['login']);
}
```

## Contributing

Contributions are welcome! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch
3. Write tests for new functionality
4. Ensure all tests pass
5. Submit a pull request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Credits

- **Author**: Roman Sidorkin
- **Email**: roman.sidorkin@gmail.com
- **Built with**: [CakePHP](https://cakephp.org) and [CakePHP Authentication](https://github.com/cakephp/authentication)

## Support

For issues, questions, or contributions, please visit:
- [GitHub Issues](https://github.com/brammo/auth/issues)
- [Documentation](https://github.com/brammo/auth)

## Changelog

### Version 1.0.0
- Initial release
- Complete authentication system
- Login/logout functionality
- Configurable routes and templates
- Password rehashing support
- Comprehensive test suite
