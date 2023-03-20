<?php
declare(strict_types=1);

namespace Ingenerator\ApiEmulator;

use GuzzleHttp\Psr7\Response;
use Ingenerator\ApiEmulator\CoreHandlers\CoreHandlerLoader;
use Psr\Http\Message\ServerRequestInterface;
use function getenv;
use function preg_match;

class HandlerLoader
{
    private array $handlers;

    public function __construct(
        private CoreHandlerLoader $core_handler_loader
    ) {
        $handlers_file = getenv('HANDLERS_FILE');
        if (empty($handlers_file)) {
            throw new \InvalidArgumentException('You must specify a HANDLERS_FILE environment variable');
        }

        $this->handlers = require($handlers_file);
    }

    public function getHandler(ServerRequestInterface $request): HandlerEntry
    {
        $match_string = $request->getMethod().' '.$request->getUri()->getPath();

        // Look for a core handler first (emulator metadata etc)
        if ($handler = $this->core_handler_loader->findHandler($match_string)) {
            return new HandlerEntry(
                '**core route**',
                match_string: $request->getMethod().' '.$request->getUri()->getPath(),
                // wrap in a closure for consistency with the custom ones
                is_core_handler: true,
                handler: fn ($r) => $handler->handle($r)
            );
        }

        // If not, find the first with a regex to match the URL
        return $this->getCustomHandler($match_string);
    }


    private function getCustomHandler(string $match_string): HandlerEntry
    {
        foreach ($this->handlers as $pattern => $handler) {
            if (preg_match($pattern, $match_string)) {
                return new HandlerEntry(
                    pattern: $pattern,
                    match_string: $match_string,
                    is_core_handler: false,
                    handler: $handler,
                );
            }
        }

        return new HandlerEntry(
            pattern: '**none**',
            match_string: $match_string,
            is_core_handler: false,
            handler: fn () => new Response(
                404,
                [],
                'No handler matched '.$match_string
            )
        );
    }

}
