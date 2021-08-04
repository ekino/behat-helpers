<?php

declare(strict_types=1);

/*
 * This file is part of the behat/helpers project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Ekino\BehatHelpers;

use Behat\Mink\Driver\CoreDriver;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Mink;
use Behat\Mink\Session;
use PHPUnit\Framework\TestCase;
use Tests\Ekino\BehatHelpers\Stub\ReloadCookiesFeatureContext;
use Tests\Ekino\BehatHelpers\Stub\SessionMockInterface;
use Tests\Ekino\BehatHelpers\Traits\TestHelperTrait;

/**
 * Class ReloadCookiesTraitTest
 *
 * @author Quentin Belot <quentin.belot@ekino.com>
 */
class ReloadCookiesTraitTest extends TestCase
{
    use TestHelperTrait;

    /**
     * Test the doOnce method.
     *
     * @param array<mixed> $tags
     * @param array<mixed> $expectedCookies
     * @param array<mixed> $cookies
     * @param array<mixed> $expectedSteps
     * @param array<mixed> $steps
     * @param int          $resetCounter
     * @param int          $sessionCounter
     * @param int          $driverCounter
     *
     * @dataProvider doOnceProvider
     *
     * @throws \ReflectionException
     */
    public function testDoOnce(array $tags, array $expectedCookies, array $cookies, array $expectedSteps, array $steps, int $resetCounter, int $sessionCounter, int $driverCounter): void
    {
        $wdSession = $this->createMock(SessionMockInterface::class);
        $wdSession->expects($this->exactly($resetCounter))->method('deleteAllCookies');

        $driver = $this->createMock(Selenium2Driver::class);
        $driver->expects($this->exactly($sessionCounter))->method('getWebDriverSession')->willReturn($wdSession);

        $session = $this->createMock(Session::class);
        $session->expects($this->exactly($driverCounter))->method('getDriver')->willReturn($driver);

        $mink = new Mink(['foo' => $session]);
        $mink->setDefaultSessionName('foo');

        $object = new ReloadCookiesFeatureContext();
        $object->setMink($mink);
        $this->setPrivatePropertyValue($object, 'tags', $tags);
        $this->setPrivatePropertyValue($object, 'cookies', $cookies);
        $this->setPrivatePropertyValue($object, 'steps', $steps);

        $object->doOnce(function() {});

        $this->assertSame($expectedCookies, $this->getPrivatePropertyValue($object, 'cookies'));
        $this->assertSame($expectedSteps, $this->getPrivatePropertyValue($object, 'steps'));
    }

    /**
     * @return \Generator<mixed>
     */
    public function doOnceProvider(): \Generator
    {
        yield [['behat_helpers_reset_cache'], [], ['cookie'], ['testDoOnce'], ['step'], 1, 1, 1];
        yield [['behat_helpers_reset_cache', 'behat_helpers_no_cache'], [], ['cookie'], [], ['step'], 1, 1, 1];
        yield [['behat_helpers_no_cache'], ['cookie'], ['cookie'], ['step'], ['step'], 0, 0, 0];
        yield [[], ['cookie'], ['cookie'], ['step', 'testDoOnce'], ['step'], 0, 1, 1];
    }

    /**
     * Tests the resetCookies method.
     */
    public function testResetCookies(): void
    {
        $wdSession = $this->createMock(SessionMockInterface::class);
        $wdSession->expects($this->once())->method('deleteAllCookies');

        $driver = $this->createMock(Selenium2Driver::class);
        $driver->expects($this->once())->method('getWebDriverSession')->willReturn($wdSession);

        $session = $this->createMock(Session::class);
        $session->expects($this->once())->method('getDriver')->willReturn($driver);

        $mink = new Mink(['foo' => $session]);
        $mink->setDefaultSessionName('foo');

        $object = new ReloadCookiesFeatureContext();
        $object->setMink($mink);
        $this->setPrivatePropertyValue($object, 'cookies', [
            'cookie1',
            'cookie2',
        ]);
        $this->setPrivatePropertyValue($object, 'steps', [
            'step1',
            'step2',
        ]);

        $this->assertTrue($this->invokeMethod($object, 'resetCookies'));
        $this->assertSame([], $this->getPrivatePropertyValue($object, 'cookies'));
        $this->assertSame([], $this->getPrivatePropertyValue($object, 'steps'));
    }

    /**
     * Tests the resetCookies method.
     */
    public function testResetCookiesWithoutCookies(): void
    {
        $wdSession = $this->createMock(SessionMockInterface::class);
        $wdSession->expects($this->never())->method('deleteAllCookies');

        $driver = $this->createMock(Selenium2Driver::class);
        $driver->expects($this->never())->method('getWebDriverSession')->willReturn($wdSession);

        $session = $this->createMock(Session::class);
        $session->expects($this->never())->method('getDriver')->willReturn($driver);

        $mink = new Mink(['foo' => $session]);
        $mink->setDefaultSessionName('foo');

        $object = new ReloadCookiesFeatureContext();
        $object->setMink($mink);
        $this->setPrivatePropertyValue($object, 'cookies', []);
        $this->setPrivatePropertyValue($object, 'steps', [
            'step1',
            'step2',
        ]);

        $this->assertFalse($this->invokeMethod($object, 'resetCookies'));
        $this->assertSame([], $this->getPrivatePropertyValue($object, 'cookies'));
        $this->assertSame([
            'step1',
            'step2',
        ], $this->getPrivatePropertyValue($object, 'steps'));
    }

    /**
     * Tests the reloadCookies method.
     */
    public function testReloadCookies(): void
    {
        $wdSession = $this->createMock(SessionMockInterface::class);
        $wdSession->expects($this->exactly(2))->method('setCookie');

        $driver = $this->createMock(Selenium2Driver::class);
        $driver->expects($this->once())->method('getWebDriverSession')->willReturn($wdSession);

        $session = $this->createMock(Session::class);
        $session->expects($this->once())->method('getDriver')->willReturn($driver);

        $mink = new Mink(['foo' => $session]);
        $mink->setDefaultSessionName('foo');

        $object = new ReloadCookiesFeatureContext();
        $object->setMink($mink);
        $this->setPrivatePropertyValue($object, 'cookies', [
            'cookie1',
            'cookie2',
        ]);

        $this->assertTrue($this->invokeMethod($object, 'reloadCookies'));
        $this->assertSame([
            'cookie1',
            'cookie2',
        ], $this->getPrivatePropertyValue($object, 'cookies'));
    }

    /**
     * Tests the reloadCookies method.
     */
    public function testReloadCookiesWithoutCookies(): void
    {
        $wdSession = $this->createMock(SessionMockInterface::class);
        $wdSession->expects($this->never())->method('setCookie');

        $driver = $this->createMock(Selenium2Driver::class);
        $driver->expects($this->never())->method('getWebDriverSession')->willReturn($wdSession);

        $session = $this->createMock(Session::class);
        $session->expects($this->never())->method('getDriver')->willReturn($driver);

        $mink = new Mink(['foo' => $session]);
        $mink->setDefaultSessionName('foo');

        $object = new ReloadCookiesFeatureContext();
        $object->setMink($mink);
        $this->setPrivatePropertyValue($object, 'cookies', []);

        $this->assertFalse($this->invokeMethod($object, 'reloadCookies'));
    }

    /**
     * Tests the assertDriverSupported method.
     */
    public function testAssertDriverSupported(): void
    {
        $driver = $this->createMock(Selenium2Driver::class);

        $session = $this->createMock(Session::class);
        $session->expects($this->once())->method('getDriver')->willReturn($driver);

        $mock = $this->createPartialMock(ReloadCookiesFeatureContext::class, ['getSession', 'assertDriverSupported']);
        $mock->expects($this->once())->method('getSession')->willReturn($session);

        $this->invokeMethod($mock, 'assertDriverSupported');
    }

    /**
     * Tests the assertDriverSupported method.
     */
    public function testAssertDriverSupportedWithException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Saving cookies only works with driver Behat\Mink\Driver\Selenium2Driver');

        $driver = $this->createMock(CoreDriver::class);

        $session = $this->createMock(Session::class);
        $session->expects($this->once())->method('getDriver')->willReturn($driver);

        $mock = $this->createPartialMock(ReloadCookiesFeatureContext::class, ['getSession', 'assertDriverSupported']);
        $mock->expects($this->once())->method('getSession')->willReturn($session);

        $this->invokeMethod($mock, 'assertDriverSupported');
    }
}
