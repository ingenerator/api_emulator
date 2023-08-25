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
            '#^\w+ /_emulator-meta/handler-data/.+$#' => fn () => new ManageHandlerDataHandler(
                '/_emulator-meta/handler-data/'
            ),
            '#^GET /_emulator-meta/health$#' => fn () => new HealthcheckHandler(),
            '#^GET /_emulator-meta/requests$#' => fn () => new ListRequestDetailsHandler($svcs->getRequestRecorder()),
        ];
    }

    public function findHandler(string $path_match_string): ?CoreHandler
    {
        foreach ($this->handler_factories as $pattern => $factory) {
            if (preg_match($pattern, $path_match_string)) {
                return $factory();
            }
        }

        return null;
    }
}
