<?php
declare(strict_types=1);

namespace Ingenerator\ApiEmulator;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use function getenv;
use function preg_match;

class HandlerLoader
{
    private array $handlers;

    public function __construct()
    {
        $handlers_file = getenv('HANDLERS_FILE');
        if (empty($handlers_file)) {
            throw new \InvalidArgumentException('You must specify a HANDLERS_FILE environment variable');
        }

        $this->handlers = require($handlers_file);
    }

    public function getHandler(ServerRequestInterface $request): HandlerEntry
    {
        $match_string = $request->getMethod().' '.$request->getUri()->getPath();
        foreach ($this->handlers as $pattern => $handler) {
            if (preg_match($pattern, $match_string)) {
                return new HandlerEntry(pattern: $pattern, match_string: $match_string, handler: $handler);
            }
        }

        return new HandlerEntry(pattern: '**none**', match_string: $match_string, handler: fn () => new Response(
            404,
            [],
            'No handler matched '.$match_string
        ));
    }

}
