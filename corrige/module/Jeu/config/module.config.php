<?php

namespace Jeu;

use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [


    // The following section is new and should be added to your file:
    'router' => [
        'routes' => [
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
            'error/404' => __DIR__ . '/../view/error/404.phtml'
        ],
        'template_path_stack' => [
            'jeu' => __DIR__ . '/../view',
        ],
    ],
];