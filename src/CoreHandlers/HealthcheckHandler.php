<?php
declare(strict_types=1);

namespace Ingenerator\ApiEmulator\CoreHandlers;

use GuzzleHttp\Psr7\Response;
use Ingenerator\ApiEmulator\HandlerRequestContext;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HealthcheckHandler implements CoreHandler
{
    public function handle(ServerRequestInterface $request, HandlerRequestContext $context): ResponseInterface
    {
        return new Response(200, ['Content-Type' => 'text/plain'], 'Emulator healthy');
    }

}
