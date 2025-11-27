<?php
declare(strict_types=1);

namespace Brammo\Auth;

use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceInterface;
use Authentication\AuthenticationServiceProviderInterface;
use Cake\Core\Configure;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Authentication Service Provider
 */
class AuthenticationServiceProvider implements AuthenticationServiceProviderInterface
{
    /**
     * Returns a service provider instance.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request
     * @return \Authentication\AuthenticationServiceInterface
     */
    public function getAuthenticationService(ServerRequestInterface $request): AuthenticationServiceInterface
    {
        // Read configuration settings
        $fields = Configure::read('Auth.Authentication.fields');
        $loginUrl = Configure::read('Auth.Routes.login');
        $userModel = Configure::read('Auth.Users.table');
        $passwordHasher = Configure::read('Auth.Authentication.passwordHasher');
        $finder = Configure::read('Auth.Authentication.finder', 'all');

        // Define identifier configuration
        $identifier = [
            'Authentication.Password' => [
                'resolver' => [
                    'className' => 'Authentication.Orm',
                    'userModel' => $userModel,
                    'finder' => $finder,
                ],
                'fields' => $fields,
                'passwordHasher' => $passwordHasher,
            ],
        ];

        // Create the authentication service
        $service = new AuthenticationService([
            'authenticators' => [
                'Authentication.Session',
                'Authentication.Form' => [
                    'fields' => $fields,
                    'loginUrl' => $loginUrl,
                    'identifier' => $identifier,
                ],
                'Authentication.Cookie' => [
                    'fields' => $fields,
                    'loginUrl' => $loginUrl,
                    'identifier' => $identifier,
                ],
            ],
            'unauthenticatedRedirect' => $loginUrl,
            'queryParam' => 'redirect',
        ]);

        return $service;
    }
}
