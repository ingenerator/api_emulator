<?php
declare(strict_types=1);

namespace Ingenerator\ApiEmulator;

use Closure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function get_debug_type;

class HandlerEntry
{
    public function __construct(
        public readonly string $pattern,
        public readonly string $match_string,
        public readonly bool $is_core_handler,
        private readonly Closure $handler,
    ) {

    }

    public function handle(ServerRequestInterface $request, HandlerRequestContext $context): ResponseInterface
    {
        $response = ($this->handler)($request, $context);
        if ( ! $response instanceof ResponseInterface) {
            throw new \UnexpectedValueException(
                'Handler mapped to '.$this->pattern.' returned '.get_debug_type($response).' - must be '.
                ResponseInterface::class
            );
        }

        return $response;
    }

}
