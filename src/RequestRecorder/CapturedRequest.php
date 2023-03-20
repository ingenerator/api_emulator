<?php
declare(strict_types=1);

namespace Ingenerator\ApiEmulator\RequestRecorder;

use JsonSerializable;

class CapturedRequest implements JsonSerializable
{

    public function __construct(
        public readonly string $handler_pattern,
        public readonly string $uri,
        public readonly string $method,
        public readonly array $headers,
        public readonly mixed $parsed_body,
    ) {
    }

    public function jsonSerialize(): mixed
    {
        return (array) $this;
    }

}
