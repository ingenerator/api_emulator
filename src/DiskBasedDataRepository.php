<?php

namespace Ingenerator\ApiEmulator;

use Ingenerator\PHPUtils\StringEncoding\JSON;
use Symfony\Component\Filesystem\Filesystem;
use function file_get_contents;

class DiskBasedDataRepository implements HandlerDataRepository
{
    public function __construct(
        private readonly string $base_dir,
        private Filesystem $filesystem = new Filesystem
    ) {

    }

    public function load(string $path): array
    {
        $file = $this->getFilename($path);
        if ( ! $this->filesystem->exists($file)) {
            throw new \UnexpectedValueException('No handler data is defined for '.$path);
        }

        return JSON::decodeArray(file_get_contents($file));
    }

    public function hasPath(string $path): bool
    {
        return $this->filesystem->exists($this->getFilename($path));
    }

    public function save(string $path, array $data): void
    {
        $this->filesystem->dumpFile($this->getFilename($path), JSON::encode($data));
    }

    public function delete(string $path): void
    {
        $this->filesystem->remove($this->getFilename($path));
    }

    private function getFilename(string $path): string
    {
        // @todo: validate the path e.g. that it does not contain . or .. elements
        return $this->base_dir.'/'.$path.'.json';
    }
}
