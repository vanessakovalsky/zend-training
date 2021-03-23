<?php

namespace Jeu;

use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\TableGateway\TableGateway;

class Module implements ConfigProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                Model\JeuTable::class => function($container) {
                    $tableGateway = $container->get(Model\JeuTableGateway::class);
                    return new Model\JeuTable($tableGateway);
                },
                Model\JeuTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Jeu());
                    return new TableGateway('jeu', $dbAdapter, null, $resultSetPrototype);
                },
            ],
        ];
    }

    // Add this method:
    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\JeuController::class => function($container) {
                    return new Controller\JeuController(
                        $container->get(Model\JeuTable::class)
                    );
                },
            ],
        ];
    }
}