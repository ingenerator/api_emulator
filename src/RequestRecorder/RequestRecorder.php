<?php
declare(strict_types=1);

namespace Ingenerator\ApiEmulator\RequestRecorder;

use Ingenerator\ApiEmulator\HandlerEntry;
use Psr\Http\Message\ServerRequestInterface;

interface RequestRecorder
{

    public function capture(ServerRequestInterface $request, HandlerEntry $emulator_handler): void;

    public function purge(): void;

    /**
     * @return CapturedRequest[]
     */
    public function listRequests(): array;
}
