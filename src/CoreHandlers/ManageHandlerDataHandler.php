<?php

namespace Ingenerator\ApiEmulator\CoreHandlers;

use GuzzleHttp\Psr7\Response;
use Ingenerator\ApiEmulator\HandlerDataRepository;
use Ingenerator\ApiEmulator\HandlerRequestContext;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function preg_quote;
use function preg_replace;

class ManageHandlerDataHandler implements CoreHandler
{
    public function __construct(private readonly string $url_path_prefix)
    {

    }

    public function handle(ServerRequestInterface $request, HandlerRequestContext $context): ResponseInterface
    {
        return match ($request->getMethod()) {
            'POST' => $this->handlePost($request, $context->data_repository),
            'DELETE' => $this->handleDelete($request, $context->data_repository),
            default => new Response(
                405,
                body: sprintf(
                    '"%s" is not a supported request method for this endpoint',
                    $request->getMethod()
                )
            )
        };
    }

    private function handlePost(
        ServerRequestInterface $request,
        HandlerDataRepository $data_repository
    ): ResponseInterface {
        $path = $this->getDataPath($request);
        $data_repository->save($path, $request->getParsedBody());

        return new Response(200, body: 'Stored handler data at '.$path);
    }

    private function handleDelete(
        ServerRequestInterface $request,
        HandlerDataRepository $data_repository
    ): ResponseInterface {
        $path = $this->getDataPath($request);
        $data_repository->delete($path);

        return new Response(204);
    }

    private function getDataPath(ServerRequestInterface $request): string
    {
        return preg_replace('#^'.preg_quote($this->url_path_prefix).'#', '', $request->getUri()->getPath());
    }

}
