<?php

namespace PartKeepr\CoreBundle\Tests\Services;

use PartKeepr\CoreBundle\Services\SystemService;

/**
 * Unit tests for the pure helper methods of the SystemService.
 *
 * These tests neither boot the Symfony kernel nor require a database, so they
 * run independently of the functional test suite.
 */
class SystemServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SystemService
     */
    private $systemService;

    public function setUp()
    {
        /*
         * format_bytes() and getBytesFromHumanReadable() don't use any of the
         * dependencies injected into the constructor, so the service can be
         * created without booting the Symfony container.
         */
        $reflection = new \ReflectionClass('PartKeepr\CoreBundle\Services\SystemService');
        $this->systemService = $reflection->newInstanceWithoutConstructor();
    }

    public function testFormatBytesKeepsSmallValuesInBytes()
    {
        $this->assertEquals('0 B', $this->systemService->format_bytes(0));
        $this->assertEquals('1 B', $this->systemService->format_bytes(1));
        $this->assertEquals('512 B', $this->systemService->format_bytes(512));
        $this->assertEquals('1023 B', $this->systemService->format_bytes(1023));
    }

    public function testFormatBytesConvertsToBinaryUnits()
    {
        $this->assertEquals('1.0 kB', $this->systemService->format_bytes(1024));
        $this->assertEquals('1.5 kB', $this->systemService->format_bytes(1536));
        $this->assertEquals('2.0 kB', $this->systemService->format_bytes(2048));
        $this->assertEquals('1.0 MB', $this->systemService->format_bytes(1048576));
        $this->assertEquals('1.9 MB', $this->systemService->format_bytes(2000000));
        $this->assertEquals('1.0 GB', $this->systemService->format_bytes(1073741824));
        $this->assertEquals('1.0 TB', $this->systemService->format_bytes(1099511627776));
    }

    public function testFormatBytesConvertsToDecimalUnits()
    {
        /*
         * The decimal conversion is currently labelled with binary prefixes
         * (KiB/MiB/...); this test documents the existing behaviour so that
         * accidental changes are noticed.
         */
        $this->assertEquals('999 B', $this->systemService->format_bytes(999, false));
        $this->assertEquals('2.0 KiB', $this->systemService->format_bytes(2048, false));
        $this->assertEquals('2.0 MiB', $this->systemService->format_bytes(2000000, false));
    }

    public function testFormatBytesReturnsNullForNonNumericValues()
    {
        $this->assertNull($this->systemService->format_bytes('not a number'));
    }

    public function testGetBytesFromHumanReadable()
    {
        $this->assertSame(1024, $this->systemService->getBytesFromHumanReadable('1K'));
        $this->assertSame(3072, $this->systemService->getBytesFromHumanReadable('3k'));
        $this->assertSame(1048576, $this->systemService->getBytesFromHumanReadable('1M'));
        $this->assertSame(1048576, $this->systemService->getBytesFromHumanReadable('1m'));
        $this->assertSame(1073741824, $this->systemService->getBytesFromHumanReadable('1G'));
    }

    public function testGetBytesFromHumanReadablePassesUnknownValuesThrough()
    {
        $this->assertSame('1024', $this->systemService->getBytesFromHumanReadable('1024'));
        $this->assertSame('foobar', $this->systemService->getBytesFromHumanReadable('foobar'));
    }
}
