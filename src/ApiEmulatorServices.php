<?php
declare(strict_types=1);

namespace Ingenerator\ApiEmulator;

use Ingenerator\ApiEmulator\CoreHandlers\CoreHandlerLoader;
use Ingenerator\ApiEmulator\HandlerData\DiskBasedDataRepository;
use Ingenerator\ApiEmulator\RequestRecorder\DiskBackedRequestRecorder;
use Ingenerator\ApiEmulator\RequestRecorder\RequestRecorder;

/**
 * Very lightweight dependency store
 */
class ApiEmulatorServices
{
    private HandlerDataRepository $data_repository;
    private RequestRecorder $request_recorder;

    private const STATE_BASE_DIRECTORY = '/var/api_emulator';

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
            $this->getDataRepository(),
            new HandlerLoader(
                new CoreHandlerLoader($this)
            )
        );
    }

    public function getRequestRecorder(): RequestRecorder
    {
        $this->request_recorder ??= new DiskBackedRequestRecorder(self::STATE_BASE_DIRECTORY);

        return $this->request_recorder;
    }

    private function getDataRepository(): HandlerDataRepository
    {
        $this->data_repository ??= new DiskBasedDataRepository(self::STATE_BASE_DIRECTORY);

        return $this->data_repository;
    }

}
