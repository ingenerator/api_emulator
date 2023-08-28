<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\Response;
use Ingenerator\PHPUtils\StringEncoding\JSON;
use Psr\Http\Message\ResponseInterface;

return [
    /**
     * Generic handler that acknowledges a message sent to the SES email API with an arbitrary message ID
     */
    '#^POST /ses/v2/email/outbound-emails$#' => function (): ResponseInterface {
        return new Response(status: 200, body: JSON::encode(['MessageId' => uniqid('', true)]));
    },
];
