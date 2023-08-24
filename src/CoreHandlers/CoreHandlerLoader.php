<?php
declare(strict_types=1);

namespace Ingenerator\ApiEmulator\CoreHandlers;

use Ingenerator\ApiEmulator\ApiEmulatorServices;
use function preg_match;

class CoreHandlerLoader
{
    private readonly array $handler_factories;

    public function __construct(
        ApiEmulatorServices $svcs
    ) {
        $this->handler_factories = [
            '#^DELETE /_emulator-meta/global-state$#' => fn () => new DeleteGlobalStateHandler(
                $svcs->getRequestRecorder()
            ),
            '#^GET /_emulator-meta/health$#' => fn () => new HealthcheckHandler(),
            '#^GET /_emulator-meta/requests$#' => fn () => new ListRequestDetailsHandler($svcs->getRequestRecorder()),
        ];
    }

    public function findHandler(string $path_match_string): ?CoreHandler
    {
        return match ($path_match_string) {
            'DELETE /_emulator-meta/global-state' => new DeleteGlobalStateHandler($this->svcs->getRequestRecorder()),
            'GET /_emulator-meta/health' => new HealthcheckHandler(),
            'GET /_emulator-meta/requests' => new ListRequestDetailsHandler($this->svcs->getRequestRecorder()),
            default => null
        };
        foreach ($this->handler_factories as $pattern => $factory) {
                return $factory();
            }
        }

        return null;
    }
}
