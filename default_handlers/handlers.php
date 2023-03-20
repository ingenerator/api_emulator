<?php

use GuzzleHttp\Psr7\Response;

return [
    // Use this when you literally just need to send some data / ping a URL and get a 200.
    // Do not use for healthchecking the emulator as that will pollute the captured app requests
    // and make your assertions harder. Instead for that you can use GET /_emulator-meta/health
    // which is not recorded with the application requests.
    '#^\w+ /ping-200$#' => fn () => new Response(
        200,
        ['Content-Type' => 'text/plain'],
        'OK'
    ),
];
