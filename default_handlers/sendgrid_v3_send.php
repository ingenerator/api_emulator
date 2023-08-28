<?php
declare(strict_types=1);

use GuzzleHttp\Psr7\Response;
use Ingenerator\PHPUtils\StringEncoding\JSON;
use Psr\Http\Message\ResponseInterface;

return [
    /**
     * Generic handler that acknowledges a message sent to the sendgrid HTTP API with a random message ID
     */
    '#^POST /sendgrid/v3/mail/send$#' => function (): ResponseInterface {
        return new Response(
            status: 202,
            headers: ['x-message-id' => uniqid('', true)],
            body: JSON::encode([])
        );
    },
];
