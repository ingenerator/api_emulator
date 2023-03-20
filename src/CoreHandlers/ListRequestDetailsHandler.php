<?php
declare(strict_types=1);

namespace Ingenerator\ApiEmulator\CoreHandlers;

use GuzzleHttp\Psr7\Response;
use Ingenerator\ApiEmulator\RequestRecorder\RequestRecorder;
use Ingenerator\PHPUtils\StringEncoding\JSON;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ListRequestDetailsHandler implements CoreHandler
{

    public function __construct(private RequestRecorder $request_recorder)
    {

    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $result = [];
        foreach ($this->request_recorder->listRequests() as $request) {
            $result[] = $request;
        }

        return new Response(200, ['Content-Type' => 'application/json'], JSON::encode($result));
    }


}
