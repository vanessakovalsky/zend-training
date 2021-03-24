<?php

declare(strict_types=1);

namespace User\Controller\Factory;

use User\Controller\AuthController;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Model\UserTable;

class AuthControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new AuthController(
            $container->get(UserTable::class)
        );
    }
}