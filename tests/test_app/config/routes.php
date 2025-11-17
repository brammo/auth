<?php
declare(strict_types=1);

/**
 * Test App routes configuration
 */

use Cake\Routing\RouteBuilder;

return function (RouteBuilder $routes): void {
    $routes->scope('/', function (RouteBuilder $builder): void {
        $builder->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);
        $builder->fallbacks();
    });
};
