<?php
declare(strict_types=1);

namespace Ingenerator\ApiEmulator\RequestRecorder;

use Ingenerator\ApiEmulator\HandlerEntry;
use Ingenerator\PHPUtils\DateTime\Clock\RealtimeClock;
use Ingenerator\PHPUtils\StringEncoding\JSON;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Filesystem\Filesystem;

class DiskBackedRequestRecorder implements RequestRecorder
{
    private Filesystem $filesystem;

    public function __construct(
        private string $base_dir,
        private RealtimeClock $clock = new RealtimeClock
    ) {
        $this->filesystem = new Filesystem;
    }

    public function capture(ServerRequestInterface $request, HandlerEntry $emulator_handler): void
    {
        $dirname = $this->base_dir.'/requests/'.$this->clock->getDateTime()->format('Y-m-d-H-i-s-u');
        $entry = new CapturedRequest(
            handler_pattern: $emulator_handler->pattern,
            uri: (string) $request->getUri(),
            method: $request->getMethod(),
            headers: $request->getHeaders(),
            parsed_body: $request->getParsedBody()
        );

        // @todo: log the raw body too, and any uploaded files???
        $this->filesystem->dumpFile($dirname.'/request.json', JSON::encode($entry));
    }

    public function purge(): void
    {
        $this->filesystem->remove($this->base_dir.'/requests');
    }

}
