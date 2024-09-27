<?php

namespace test\unit\HandlerData;

use Ingenerator\ApiEmulator\HandlerData\HandlerDataPathValidator;
use PHPUnit\Framework\TestCase;

class HandlerDataPathValidatorTest extends TestCase
{

    public static function provider_safe_paths()
    {
        return [
            'simple name' => ['users', true],
            'nested name' => ['users/whatever', true],
            'deep nested name' => ['users/whatever/else/is/fine', true],
            'including numbers' => ['users/237', true],
            'including hyphen' => ['users/237/some-data', true],
            'including underscore' => ['users/237/some_data', true],
            'no whitespace' => ['users/what ever', false],
            'no special chars' => ['users/what[ever]', false],
            'no parent path' => ['users/../../home/root/uhoh', false],
            'no relative path' => ['users/./something', false],
            'no file extensions' => ['this/is-ok-but-confusing.json', false],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('provider_safe_paths')]
    public function test_it_only_validates_safe_paths(string $path, bool $expect_valid): void
    {
        $this->assertSame($expect_valid, HandlerDataPathValidator::isValid($path));

    }
}
