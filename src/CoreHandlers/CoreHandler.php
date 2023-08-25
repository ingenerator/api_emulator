<?php
declare(strict_types=1);

namespace Ingenerator\ApiEmulator\CoreHandlers;

use Ingenerator\ApiEmulator\HandlerRequestContext;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface CoreHandler
{
    public function handle(ServerRequestInterface $request, HandlerRequestContext $context): ResponseInterface;

}
