<?php
declare(strict_types=1);

namespace Ingenerator\ApiEmulator\CoreHandlers;

use GuzzleHttp\Psr7\Response;
use Ingenerator\ApiEmulator\HandlerRequestContext;
use Ingenerator\ApiEmulator\RequestRecorder\RequestRecorder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DeleteGlobalStateHandler implements CoreHandler
{

    public function __construct(
        private RequestRecorder $request_recorder
    ) {

    }

    public function handle(ServerRequestInterface $request, HandlerRequestContext $context): ResponseInterface
    {
        $this->request_recorder->purge();

        return new Response(204, [], 'State purged');
    }


}
