<?php

declare(strict_types=1);

namespace Klax\HttpKernel\Runner;

use Klax\Container\Contract\Configuration\ArrayFileLoaderInterface;
use Klax\Container\Contract\Configuration\ClosureFileLoaderInterface;
use Klax\Container\Contract\ContainerInterface;
use Klax\Http\Router\Contract\RouteCollectionInterface;
use Klax\Http\Runner\Contract\HttpRunnerInterface;
use Klax\HttpKernel\Runner\Contract\HttpKernelInterface;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class HttpKernel implements HttpKernelInterface
{
    public function __construct(
        protected ArrayFileLoaderInterface $arrayFileLoader,
        protected ClosureFileLoaderInterface $closureFileLoader,
        protected RouteCollectionInterface $routeCollection,
    ) {
    }

    public function bootContainer(ContainerInterface $container, string $servicesFilesMap): void
    {
        // primitive loading

        foreach ($this->arrayFileLoader->load($servicesFilesMap) as $serviceFile) {
            foreach ($this->arrayFileLoader->load($serviceFile) as $id => $service) {
                $container->set($id, $service);
            }
        }
    }

    public function bootRoutes(string $routesFilesMap): void
    {
        // primitive loading

        foreach ($this->arrayFileLoader->load($routesFilesMap) as $routesFile) {
            foreach ($this->closureFileLoader->load($routesFile) as $routeConfiguratorClosure) {
                $routeConfiguratorClosure($this->routeCollection);
            }
        }
    }

    public function run(PsrContainerInterface $container): void
    {
        $container->get(HttpRunnerInterface::class)->run($container->get(
            ServerRequestInterface::class,
        ));
    }
}
