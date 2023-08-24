<?php

namespace Ingenerator\ApiEmulator;

interface HandlerDataRepository
{
    public function hasPath(string $path): bool;

    public function load(string $path): array;

    public function save(string $path, array $data): void;

    public function delete(string $path): void;


}
