<?php

namespace Tests\Unit\Adapter\Administration\Slim;

use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use stdClass;

class ControllerTest extends TestCase
{
    private ContainerInterface $container;
    private TestController $controller;

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function setUp(): void
    {
        $this->container = $this->createStub(ContainerInterface::class);

        $this->controller = new TestController($this->container);
    }

    public function testGetSuccessful()
    {
        $object = new stdClass();
        $this->container->method('get')
            ->willReturn($object);

        $result = $this->controller->testGet(stdClass::class);
        $this->assertEquals($object, $result);
    }

    public function testGetWithNotFoundException()
    {
        $notFoundException = new class () extends Exception implements NotFoundExceptionInterface {};
        $this->container->method('get')
            ->willThrowException($notFoundException);

        $result = $this->controller->testGet('non-existent-class');
        $this->assertNull($result);
    }

    public function testGetWithContainerException()
    {
        $containerException = new class () extends Exception implements ContainerExceptionInterface {};
        $this->container->method('get')
            ->willThrowException($containerException);

        $result = $this->controller->testGet('problematic-class');
        $this->assertNull($result);
    }
}
