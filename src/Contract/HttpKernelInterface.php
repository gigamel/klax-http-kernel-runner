<?php

declare(strict_types=1);

namespace Klax\HttpKernel\Runner\Contract;

use Klax\Container\Contract\KernelInterface as BootableKernelInterface;
use Klax\HttpKernel\Contract\HttpKernelInterface as RoutableKernelInterface;
use Klax\Kernel\Contract\KernelInterface as RunnableKernelInterface;

interface HttpKernelInterface extends RoutableKernelInterface, BootableKernelInterface, RunnableKernelInterface
{
}
