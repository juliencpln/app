<?php


$router = $di->getRouter();

// $router->add(
//     '/login',
//     [
//         'controller' => 'login',
//         'action'     => 'index',
//     ]
// );

// $router->add(
//     '/logout',
//     [
//         'controller' => 'login',
//         'action'     => 'index',
//     ]
// );


// $router->add(
//     '/companies',
//     [
//         'controller' => 'companies',
//     ]
// );



$router->handle($_SERVER['REQUEST_URI']);
