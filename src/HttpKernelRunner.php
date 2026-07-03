<?php

declare(strict_types=1);

namespace Klax\HttpKernel\Runner;

use Klax\Container\Container;
use Klax\Container\Contract\ContainerInterface;
use Klax\HttpKernel\Runner\Contract\BootThrowableHandlerInterface;
use Klax\HttpKernel\Runner\Contract\HttpKernelInterface;
use Throwable;

class HttpKernelRunner
{
    protected BootThrowableHandlerInterface $bootThrowableHandler;

    public function __construct(
        protected ContainerInterface $container,
        protected string $servicesFilesMap,
        protected string $routesFilesMap,
        protected ?BootThrowableHandlerInterface $bootThrowableHandler = null,
    ) {
        $this->bootThrowableHandler = $bootThrowableHandler ?? new PrimitiveBootThrowableHandler();
    }

    public static function withDefaultContainer(
        string $servicesFilesMap,
        string $routesFilesMap,
        ?BootThrowableHandlerInterface $bootThrowableHandler = null,
    ): self {
        return new self(new Container(), $servicesFilesMap, $routesFilesMap, $bootThrowableHandler);
    }

    public function run(): void
    {
        try {
            $httpKernel = $this->container->get(HttpKernelInterface::class);

            $httpKernel->bootContainer($this->servicesFilesMap);
            $httpKernel->bootRoutes($this->routesFilesMap);

            $httpKernel->run($this->container);
        } catch (Throwable $bootThrowable) {
            $this->bootThrowableHandler->handle($bootThrowable);
        }
    }
}
