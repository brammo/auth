<?php
declare(strict_types=1);

namespace Brammo\Auth\Controller;

use Authentication\Identifier\IdentifierCollection;
use Authentication\Identifier\PasswordIdentifier;
use Brammo\Auth\Model\Entity\User;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Response;

/**
 * Auth User Controller
 *
 * Handles user authentication actions such as login and logout.
 */
class UserController extends AppController
{
    /**
     * Called before the controller action
     *
     * @param \Cake\Event\EventInterface<\Cake\Controller\Controller> $event An Event instance
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated(['login']);
    }

    /**
     * Displays user login form
     *
     * @return \Cake\Http\Response|null Response with redirection on success login
     */
    public function login(): ?Response
    {
        $this->getRequest()->allowMethod(['get', 'post']);

        $result = $this->Authentication->getResult();

        // If the user is logged in, redirect to the configured location
        if ($result && $result->isValid()) {
            $this->rehashUserPassword();

            // Redirect to the originally intended URL or default location defined in configuration
            $redirectUrl = $this->getRequest()->getQuery(
                'redirect',
                $this->Authentication->getLoginRedirect() ??
                Configure::read('Auth.Routes.loginRedirect', '/'),
            );

            return $this->redirect($redirectUrl);
        }

        if ($this->getRequest()->is('post')) {
            $this->showLoginError();
        }

        // Render the login template defined in configuration
        $template = Configure::read('Auth.Templates.login');
        if ($template) {
            $this->viewBuilder()->setTemplate($template);
        }

        return null;
    }

    /**
     * Logs out the user
     *
     * @return \Cake\Http\Response Response with redirection
     */
    public function logout(): ?Response
    {
        $this->Authentication->logout();

        // Redirect to the login page after logout
        $loginUrl = Configure::read('Auth.Routes.login', '/login');

        return $this->redirect($loginUrl);
    }

    /**
     * Rehash password if needed
     *
     * @return void
     */
    private function rehashUserPassword(): void
    {
        // Check if password rehashing is enabled
        if (!Configure::read('Auth.Authentication.rehashPasswords', false)) {
            return;
        }

        // Get the authentication service
        $authentication = $this->Authentication->getAuthenticationService();

        // Get the identifier from the successful authentication provider
        $provider = $authentication->getAuthenticationProvider();
        if ($provider === null) {
            return;
        }

        $identifiers = $provider->getIdentifier();

        // Get the Password identifier from the collection
        if (!$identifiers instanceof IdentifierCollection || !$identifiers->has('Password')) {
            return;
        }

        $passwordIdentifier = $identifiers->get('Password');
        if (!$passwordIdentifier instanceof PasswordIdentifier) {
            return;
        }

        // Check if the password needs rehashing
        if ($passwordIdentifier->needsPasswordRehash()) {
            // Get the currently logged-in user's ID
            $identity = $authentication->getIdentity();
            if ($identity === null) {
                return;
            }
            $userId = $identity->getIdentifier();

            // Get user table from configuration
            $Users = $this->fetchTable(Configure::read('Auth.Users.table'));

            // Update the user's password
            $user = $Users->get($userId);
            $user->set('password', $this->getRequest()->getData('password'));
            $Users->save($user);
        }
    }

    /**
     * Show appropriate login error message based on user status
     *
     * Checks if the user exists and displays a specific message
     * based on their account status (blocked, not activated, or invalid credentials).
     *
     * @return void
     */
    private function showLoginError(): void
    {
        $email = $this->getRequest()->getData('email');
        $messages = Configure::read('Auth.Messages', []);
        $enumerateAccounts = Configure::read('Auth.Messages.enumerateAccounts', true);

        if ($enumerateAccounts && $email) {
            $Users = $this->fetchTable(Configure::read('Auth.Users.table'));
            $user = $Users->find()
                ->where(['email' => $email])
                ->first();

            if ($user instanceof User) {
                if ($user->isBlocked()) {
                    $this->Flash->error(__($messages['blocked'] ?? 'Your account has been blocked'));

                    return;
                }

                if ($user->isStatusNew()) {
                    $this->Flash->error(__($messages['notActivated'] ?? 'Your account is not yet activated'));

                    return;
                }
            }
        }

        $this->Flash->error(__($messages['invalidCredentials'] ?? 'Invalid email or password'));
    }
}
