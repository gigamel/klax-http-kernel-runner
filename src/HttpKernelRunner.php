<?php

declare(strict_types=1);

namespace Klax\HttpKernel\Runner;

use Klax\Container\Container;
use Klax\Container\Contract\ContainerInterface;
use Klax\HttpKernel\Runner\Contract\BootThrowableHandlerInterface;
use Klax\HttpKernel\Runner\Contract\HttpKernelInterface;
use RuntimeException;
use Throwable;

class HttpKernelRunner
{
    protected BootThrowableHandlerInterface $bootThrowableHandler;

    public function __construct(
        protected ContainerInterface $container,
        protected string $preBootFilesMap,
        protected string $servicesFilesMap,
        protected string $routesFilesMap,
        ?BootThrowableHandlerInterface $bootThrowableHandler = null,
    ) {
        $this->bootThrowableHandler = $bootThrowableHandler ?? new PrimitiveBootThrowableHandler();
    }

    public static function withDefaultContainer(
        string $preBootFilesMap,
        string $servicesFilesMap,
        string $routesFilesMap,
        ?BootThrowableHandlerInterface $bootThrowableHandler = null,
    ): self {
        return new self(new Container(), $preBootFilesMap, $servicesFilesMap, $routesFilesMap, $bootThrowableHandler);
    }

    public function run(): void
    {
        try {
            $this->preBoot(); // Todo
            $httpKernel = $this->container->get(HttpKernelInterface::class);

            $httpKernel->bootContainer($this->container, $this->servicesFilesMap);
            $httpKernel->bootRoutes($this->routesFilesMap);

            $httpKernel->run($this->container);
        } catch (Throwable $bootThrowable) {
            $this->bootThrowableHandler->handle($bootThrowable);
        }
    }

    /**
     * @throws RuntimeException
     */
    protected function preBoot(): void
    {
        if (!is_file($this->preBootFilesMap) || !is_readable($this->preBootFilesMap)) {
            throw new RuntimeException(sprintf(
                'Pre-boot file "%s" does not exist or is not readable',
                $this->preBootFilesMap,
            ));
        }

        foreach ((array)require_once($this->preBootFilesMap) as $id => $service) {
            $this->container->set($id, $service);
        }
    }
}
