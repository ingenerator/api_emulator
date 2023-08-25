<?php

namespace test\unit\CoreHandlers;

use Ingenerator\ApiEmulator\ApiEmulatorServices;
use Ingenerator\ApiEmulator\CoreHandlers\CoreHandlerLoader;
use Ingenerator\ApiEmulator\CoreHandlers\DeleteGlobalStateHandler;
use Ingenerator\ApiEmulator\CoreHandlers\HealthcheckHandler;
use Ingenerator\ApiEmulator\CoreHandlers\ListRequestDetailsHandler;
use Ingenerator\ApiEmulator\CoreHandlers\ManageHandlerDataHandler;
use PHPUnit\Framework\TestCase;

class CoreHandlerLoaderTest extends TestCase
{

    public function test_it_is_initialisable()
    {
        $this->assertInstanceOf(CoreHandlerLoader::class, $this->newSubject());
    }

    public static function provider_expected_handlers()
    {
        return [
            // Valid cases
            ['DELETE /_emulator-meta/global-state', DeleteGlobalStateHandler::class],
            ['DELETE /_emulator-meta/handler-data/custom', ManageHandlerDataHandler::class],
            ['DELETE /_emulator-meta/handler-data/custom/nested1523', ManageHandlerDataHandler::class],
            ['POST /_emulator-meta/handler-data/custom', ManageHandlerDataHandler::class],
            ['POST /_emulator-meta/handler-data/custom/nested1523', ManageHandlerDataHandler::class],
            ['GET /_emulator-meta/handler-data/custom', ManageHandlerDataHandler::class],
            ['GET /_emulator-meta/handler-data/custom/nested-1523', ManageHandlerDataHandler::class],
            ['GET /_emulator-meta/health', HealthcheckHandler::class],
            ['GET /_emulator-meta/requests', ListRequestDetailsHandler::class],
            // Unknown routes
            ['POST /_emulator-meta/global-state', null],
            ['DELETE /_emulator-meta/handler-data', null],
            ['POST /_emulator-meta/health', null],
            ['GET /_emulator-meta/health/junk', null],
            ['POST /_emulator-meta/requests', null],
        ];
    }

    /**
     * @dataProvider provider_expected_handlers
     */
    public function test_it_returns_expected_handlers(string $path_match_string, ?string $expect_handler)
    {
        $result = $this->newSubject()->findHandler($path_match_string);

        if ($expect_handler === null) {
            $this->assertNull($result);
        } else {
            $this->assertInstanceOf($expect_handler, $result);
        }
    }

    private function newSubject(): CoreHandlerLoader
    {
        return new CoreHandlerLoader(
            new ApiEmulatorServices()
        );
    }

}
