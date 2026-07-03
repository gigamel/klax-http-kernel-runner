<?php

declare(strict_types=1);

namespace Klax\HttpKernel\Runner;

use Klax\HttpKernel\Runner\Contract\BootThrowableHandlerInterface;
use Throwable;

final readonly class PrimitiveBootThrowableHandler implements BootThrowableHandlerInterface
{

    public function handle(Throwable $bootThrowable): void
    {
        echo '<pre>';

        var_dump([
            'type' => $bootThrowable::class,
            'message' => $bootThrowable->getMessage(),
            'file' => sprintf(
                '%s::%d',
                $bootThrowable->getFile(),
                $bootThrowable->getLine(),
            ),
        ]);

        echo '</pre>';
    }
}
