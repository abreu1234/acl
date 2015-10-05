<?php
use Cake\Routing\Router;

Router::plugin('Acl', function ($routes) {
    $routes->fallbacks('DashedRoute');
});
