<?php

namespace User;

use Laminas\Db\Adapter\Adapter;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\TableGateway\TableGateway;
use User\Service\AuthManager;

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
                Model\UserTable::class => function($container) {
                    $adapter = $container->get(Adapter::class);
                    return new Model\UserTable($adapter);
                },
                // Model\UserTableGateway::class => function ($container) {
                //     $dbAdapter = $container->get(AdapterInterface::class);
                //     $resultSetPrototype = new ResultSet();
                //     $resultSetPrototype->setArrayObjectPrototype(new Model\User());
                //     return new TableGateway('user', $dbAdapter, null, $resultSetPrototype);
                // },
            ],
        ];
    }

    public function getControllerConfig()
    {

    }
}