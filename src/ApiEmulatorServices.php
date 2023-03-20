<?php
declare(strict_types=1);

namespace Ingenerator\ApiEmulator;

use Ingenerator\ApiEmulator\RequestRecorder\DiskBackedRequestRecorder;
use Ingenerator\ApiEmulator\RequestRecorder\RequestRecorder;

/**
 * Very lightweight dependency store
 */
class ApiEmulatorServices
{
    private RequestRecorder $request_recorder;

    public static function instance()
    {
        static $instance;
        $instance ??= new static;

        return $instance;
    }

    public function makeRequestExecutor(): RequestExecutor
    {
        return new RequestExecutor(
            Logger::instance(),
            $this->getRequestRecorder(),
        );
    }

    public function getRequestRecorder(): RequestRecorder
    {
        $this->request_recorder ??= new DiskBackedRequestRecorder(
            '/var/api_emulator',
        );

        return $this->request_recorder;
    }

}
