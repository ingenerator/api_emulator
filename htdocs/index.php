<?php

use GuzzleHttp\Psr7\ServerRequest;
use Ingenerator\ApiEmulator\ApiEmulatorServices;
use Ingenerator\ApiEmulator\Logger;

try {
    ob_start();

    require_once __DIR__.'/../vendor/autoload.php';

    Logger::initialise(fn () => new Logger('php://stderr'));

    $response = ApiEmulatorServices::instance()
        ->makeRequestExecutor()
        ->execute(ServerRequest::fromGlobals());

    $output = ob_get_clean();
    if ($output !== '') {
        throw new \InvalidArgumentException("Unexpected output during request:\n".$output);
    }

    http_response_code($response->getStatusCode());
    foreach ($response->getHeaders() as $header => $header_values) {
        foreach ($header_values as $value) {
            header($header.': '.$value, true);
        }
    }
    echo $response->getBody()->getContents();

} catch (Throwable $e) {
    if (class_exists(Logger::class, false)) {
        Logger::instance()->emergency($e->getMessage());
    } else {
        file_put_contents('php://stderr', "FATAL: ".get_class($e)." ".$e->getMessage()."\n");

    }

    http_response_code(500);
}
