<?php
declare(strict_types=1);

namespace Ingenerator\ApiEmulator;

use Ingenerator\PHPUtils\DateTime\DateString;
use Ingenerator\PHPUtils\Object\InitialisableSingletonTrait;
use Psr\Log\AbstractLogger;
use function file_put_contents;
use function sprintf;

class Logger extends AbstractLogger
{
    use InitialisableSingletonTrait;

    public function __construct(
        private string $target
    ) {
    }

    public function log($level, $message, array $context = [])
    {
        file_put_contents(
            $this->target,
            sprintf(
                "%s [%s] %s\n",
                DateString::format(new \DateTimeImmutable, 'm-d H:i:s.u'),
                $level,
                $message
            )
        );
    }

}
