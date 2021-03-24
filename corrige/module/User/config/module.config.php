<?php

namespace User;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use User\Controller\Factory\AuthControllerFactory;
use User\Controller\Factory\LoginControllerFactory;

return [
    // The following section is new and should be added to your file:
    'router' => [
        'routes' => [
            'signup' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/signup',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'create',
                    ],
                ],
            ],
            'user' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/user[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\LoginController::class,
                        'action'     => 'login',
                    ],
                ],
            ],
        ],
    ],

    'controllers' => [
        'factories' => [
            Controller\AuthController::class => AuthControllerFactory::class,
            Controller\LoginController::class => LoginControllerFactory::class,
        ],
    ],

    'view_manager' => [
        'exception_template'      => 'error/index',
        'template_map' => [
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
            'auth/create'             => __DIR__ . '/../view/user/auth/create.phtml',
        ],
        'template_path_stack' => [
            'user' => __DIR__ . '/../view',
        ],
    ],

];