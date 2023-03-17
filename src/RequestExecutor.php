<?php
declare(strict_types=1);

namespace Ingenerator\ApiEmulator;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class RequestExecutor
{
    public function __construct(
        private LoggerInterface $logger,
        private HandlerLoader $handler_loader = new HandlerLoader
    ) {
    }

    public function execute(ServerRequestInterface $request): ResponseInterface
    {
        $handler = $this->handler_loader->getHandler($request);
        $response = $handler->handle($request);

        return $response;
    }

}
