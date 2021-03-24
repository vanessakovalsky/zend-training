<?php

namespace Jeu;

use Laminas\Router\Http\Segment;
use Laminas\Router\Http\Literal;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [


    // The following section is new and should be added to your file:
    'router' => [
        'routes' => [
            'home' => [
                'type'    => Literal::class,
                'options' => [
                    'route' => '/',
                    'defaults' => [
                        'controller' => Controller\JeuController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'jeu' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/jeu[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\JeuController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],

    'view_manager' => [
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'not_found_template'      => __DIR__ . '/../view/error/404',
            'exception_template'      => __DIR__ . '/../view/error/index',
        ],
        'template_path_stack' => [
            'jeu' => __DIR__ . '/../view',
        ],
    ],
];