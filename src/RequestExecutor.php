<?php
declare(strict_types=1);

namespace Ingenerator\ApiEmulator;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use function implode;

class RequestExecutor
{
    public function __construct(
        private LoggerInterface $logger,
        private HandlerLoader $handler_loader = new HandlerLoader,
        private JSONRequestBodyParser $json_body_parser = new JSONRequestBodyParser,
    ) {
    }

    public function execute(ServerRequestInterface $request): ResponseInterface
    {
        $handler = $this->handler_loader->getHandler($request);

        $response = $handler->handle(
            // Guzzle doesn't natively decode JSON bodies
            $this->json_body_parser->parse($request)
        );

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
