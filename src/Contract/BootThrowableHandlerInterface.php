<?php

declare(strict_types=1);

namespace Klax\HttpKernel\Runner\Contract;

use Throwable;

interface BootThrowableHandlerInterface
{
    public function handle(Throwable $bootThrowable): void;
}
