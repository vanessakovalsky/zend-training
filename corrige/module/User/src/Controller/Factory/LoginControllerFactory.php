<?php

declare(strict_types=1);

namespace User\Controller\Factory;

use User\Controller\LoginController;
use Interop\Container\ContainerInterface;
use Laminas\Db\Adapter\Adapter;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Model\UserTable;

class LoginControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new LoginController(
            $container->get(Adapter::class),
            $container->get(UserTable::class)
        );
    }
}