<?php
declare(strict_types=1);

namespace Ingenerator\ApiEmulator;

use Ingenerator\ApiEmulator\RequestRecorder\RequestRecorder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use function implode;

class RequestExecutor
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly RequestRecorder $request_recorder,
        private readonly HandlerDataRepository $data_repository,
        private readonly HandlerLoader $handler_loader,
        private readonly JSONRequestBodyParser $json_body_parser = new JSONRequestBodyParser,
    ) {
    }

    public function execute(ServerRequestInterface $request): ResponseInterface
    {
        $handler = $this->handler_loader->getHandler($request);
        try {
            // Guzzle doesn't natively decode JSON bodies
            $request = $this->json_body_parser->parse($request);
            $response = $handler->handle(
                $request,
                new HandlerRequestContext(
                    data_repository: $this->data_repository
                )
            );
        } finally {
            if ( ! $handler->is_core_handler) {
                // Only log custom requests, not emulator metadata etc
                $this->request_recorder->capture($request, $handler);
            }
        }

        $this->logRequest($request, $handler, $response);

        return $response;
    }

    private function logRequest(
        ServerRequestInterface $request,
        HandlerEntry $handler,
        ResponseInterface $response
    ): void {
        $this->logger->info(
            implode(' ', [
                $request->getMethod(),
                $request->getUri(),
                'matched='.$handler->pattern,
                '=>',
                $response->getStatusCode(),
                $response->getHeaderLine('Content-Type'),
            ])
        );
    }

}
