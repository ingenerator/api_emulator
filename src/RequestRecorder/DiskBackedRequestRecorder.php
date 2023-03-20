<?php
declare(strict_types=1);

namespace Ingenerator\ApiEmulator\RequestRecorder;

use Ingenerator\ApiEmulator\HandlerEntry;
use Ingenerator\PHPUtils\DateTime\Clock\RealtimeClock;
use Ingenerator\PHPUtils\StringEncoding\JSON;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Filesystem\Filesystem;
use function file_get_contents;

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
        $request_id = $this->clock->getDateTime()->format('Y-m-d-H-i-s-u');
        $dirname = $this->base_dir.'/requests/'.$request_id;
        $entry = new CapturedRequest(
            id: $request_id,
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

    /**
     * @inheritDoc
     */
    public function listRequests(): array
    {
        $files = glob($this->base_dir.'/requests/*/request.json');

        return array_map(
            fn ($file) => new CapturedRequest(...JSON::decodeArray(file_get_contents($file))),
            $files
        );
    }
}
