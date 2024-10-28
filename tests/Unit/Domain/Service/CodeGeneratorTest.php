<?php

namespace Tests\Unit\Domain\Service;

use App\Domain\Service\CodeGenerator;
use PHPUnit\Framework\TestCase;

class CodeGeneratorTest extends TestCase
{
    public function testGenerateCode(): void
    {
        $code = CodeGenerator::generate();

        $this->assertIsString($code);
        $this->assertStringNotContainsString('-', $code);
    }
}
