<?php
declare(strict_types=1);

namespace Brammo\Auth\Controller;

use Brammo\Auth\Controller\AppController;
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
     * @return \Cake\Http\Response|null|void
     */
    public function beforeFilter(EventInterface $event)
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
        $this->request->allowMethod(['get', 'post']);

        $result = $this->Authentication->getResult();

        // If the user is logged in, redirect to the configured location
        if ($result && $result->isValid()) {

            $this->rehashUserPassword();

            // Redirect to the originally intended URL or default location defined in configuration
            $redirectUrl = $this->request->getQuery('redirect',
                $this->Authentication->getLoginRedirect() ??
                Configure::read('Auth.Routes.loginRedirect', '/')
            );

            return $this->redirect($redirectUrl);
        }

        if ($this->request->is('post')) {
            $this->Flash->error(__('Invalid email or password'));
        }

        // Render the login template defined in configuration
        $template = Configure::read('Auth.Templates.login');
        $this->render($template);
        
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
    private function rehashUserPassword()
    {
        // Get the authentication service
        $authentication = $this->Authentication->getAuthenticationService();

        // Check if the password needs rehashing
        if ($authentication->identifiers()->get('Password')->needsPasswordRehash()) {

            // Get the currently logged-in user's ID
            $userId = $authentication->getIdentity()->getIdentifier();

            // Get user table from configuration
            $Users = $this->fetchTable(Configure::read('Auth.Users.table'));

            // Update the user's password
            $user = $Users->get($userId);
            $user->password = $this->request->getData('password');
            $Users->save($user);
        }
    }
}
