<?php
declare(strict_types=1);

namespace Brammo\Auth\Controller;

use Cake\Controller\Controller;

/**
 * Application Controller
 * 
 * Base controller class for the Brammo/Auth plugin.
 */
class AppController extends Controller
{
    /**
     * Initialization
     * 
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        // Load the Authentication component
        $this->loadComponent('Authentication.Authentication');
    }
}
