<?php

namespace Tests\Unit\Domain\Administrator;

use App\Domain\Administrator\Administrator;
use App\Domain\Administrator\AdministratorCode;
use PHPUnit\Framework\TestCase;

class AdministratorTest extends TestCase
{
    private int $id;
    private AdministratorCode $code;
    private string $name;
    private string $email;

    protected function setUp(): void
    {
        $this->id = 1;
        $this->code = new AdministratorCode('12345678-90ab-4cde-8123-567890abcdef');
        $this->name = 'Foo Boo Kee';
        $this->email = 'fbk@example.com';
    }

    public static function createAdministrator(
        int $id = 1,
        AdministratorCode $code = new AdministratorCode('12345678-90ab-4cde-8123-567890abcdef'),
        string $name = 'Foo Boo Kee',
        string $email = 'fbk@example.com'
    ): Administrator {
        return new Administrator($id, $code, $name, $email);
    }

    private function createTestAdministrator(): Administrator
    {
        return self::createAdministrator(
            $this->id,
            $this->code,
            $this->name,
            $this->email
        );
    }

    public function testConstructor(): void
    {
        $administrator = $this->createTestAdministrator();

        $this->assertEquals($this->id, $administrator->getId());
        $this->assertEquals($this->code->getValue(), $administrator->getCode()->getValue());
        $this->assertEquals($this->name, $administrator->getName());
        $this->assertEquals($this->email, $administrator->getEmail());
    }

    public function testConstructorWithNullIdAndCode(): void
    {
        $administrator = new Administrator(null, null, $this->name, $this->email);

        $this->assertNull($administrator->getId());
        $this->assertNull($administrator->getCode());
        $this->assertEquals($this->name, $administrator->getName());
        $this->assertEquals($this->email, $administrator->getEmail());
    }

    public function testSetters(): void
    {
        $administrator = $this->createTestAdministrator();
        $newCode = new AdministratorCode('98765432-10ab-4cde-8123-567890abcdef');
        $newName = 'Shee Rakami';
        $newEmail = 'srkm@example.com';

        $administrator->setCode($newCode);
        $administrator->setName($newName);
        $administrator->setEmail($newEmail);

        $this->assertEquals($newCode, $administrator->getCode());
        $this->assertEquals($newName, $administrator->getName());
        $this->assertEquals($newEmail, $administrator->getEmail());
    }

    public function testSerialize(): void
    {
        $administrator = $this->createTestAdministrator();

        $expected = [
            'code' => $this->code->getValue(),
            'name' => $this->name,
            'email' => $this->email,
        ];

        $this->assertEquals($expected, $administrator->serialize());
    }
}
