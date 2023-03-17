<?php

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

return [
    '#^\w+ /health$#' => fn (ServerRequestInterface $request): ResponseInterface => new Response(
        200,
        ['Content-Type' => 'text/plain'],
        'Emulator healthy'
    ),
];
