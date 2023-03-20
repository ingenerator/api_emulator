<?php
declare(strict_types=1);

namespace Ingenerator\ApiEmulator\CoreHandlers;

use Ingenerator\ApiEmulator\ApiEmulatorServices;

class CoreHandlerLoader
{
    public function __construct(
        private ApiEmulatorServices $svcs
    ) {
    }

    public function findHandler(string $path_match_string): ?CoreHandler
    {
        return match ($path_match_string) {
            'DELETE /_emulator-meta/global-state' => new DeleteGlobalStateHandler($this->svcs->getRequestRecorder()),
            'GET /_emulator-meta/health' => new HealthcheckHandler(),
            'GET /_emulator-meta/requests' => new ListRequestDetailsHandler($this->svcs->getRequestRecorder()),
            default => null
        };
    }
}
