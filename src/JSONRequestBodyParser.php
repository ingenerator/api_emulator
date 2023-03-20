<?php
declare(strict_types=1);

namespace Ingenerator\ApiEmulator;

use Ingenerator\PHPUtils\StringEncoding\JSON;
use Psr\Http\Message\ServerRequestInterface;
use function strtok;

class JSONRequestBodyParser
{

    public function parse(ServerRequestInterface $request): ServerRequestInterface
    {
        if ($this->hasJsonContent($request)) {
            if ( ! empty($request->getParsedBody())) {
                throw new \UnexpectedValueException(
                    'Can\'t automatically parse request with application/json content-type as it already has a parsed body'
                );
            }

            $json = $this->readJsonBodyContent($request);

            return $request->withParsedBody(JSON::decode($json));
        }

        return $request;
    }

    private function hasJsonContent(ServerRequestInterface $request): bool
    {
        $content_type = $request->getHeaderLine('Content-Type');
        // ignore charset etc
        $media = strtok($content_type, ';');

        return $media === 'application/json';
    }

    private function readJsonBodyContent(ServerRequestInterface $request): string
    {
        // Reading the body leaves the pointer at the end so we have to explicitly rewind after we've
        // read it to avoid mutating state.
        try {
            return $request->getBody()->getContents();
        } finally {
            $request->getBody()->rewind();
        }

    }
}
