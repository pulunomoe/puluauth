<?php

namespace Tests\Unit\Domain\Administrator;

use App\Domain\Administrator\AdministratorCode;
use PHPUnit\Framework\TestCase;

class AdministratorCodeTest extends TestCase
{
    public function testGetValue(): void
    {
        $code = '12345678-90ab-4cde-8123-567890abcdef';

        $administratorCode = new AdministratorCode($code);

        $this->assertEquals($code, $administratorCode->getValue());
    }
}
