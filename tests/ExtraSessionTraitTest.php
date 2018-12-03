<?php

/*
 * This file is part of the behat/helpers project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Ekino\BehatHelpers;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Session;
use Ekino\BehatHelpers\ExtraSessionTrait;
use PHPUnit\Framework\TestCase;

/**
 * @author Rémi Marseille <remi.marseille@ekino.com>
 */
class ExtraSessionTraitTest extends TestCase
{
    /**
     * Tests the maximizeWindowOnBeforeScenario method.
     */
    public function testMaximizeWindowOnBeforeScenario()
    {
        $driver  = $this->createMock(DriverInterface::class);
        $session = $this->createMock(Session::class);
        $driver->expects($this->once())->method('maximizeWindow');
        $session->expects($this->once())->method('getDriver')->willReturn($driver);

        $mock = $this->getExtraSessionMock();
        $mock->expects($this->once())->method('getSession')->willReturn($session);

        $mock->maximizeWindowOnBeforeScenario();
    }

    /**
     * Tests the waitForSeconds method.
     */
    public function testWaitForSeconds()
    {
        $session = $this->createMock(Session::class);
        $session->expects($this->once())->method('wait')->with($this->equalTo(1000));

        $mock = $this->getExtraSessionMock();
        $mock->expects($this->once())->method('getSession')->willReturn($session);

        $mock->waitForSeconds(1);
    }

    /**
     * Tests the iWaitForCssElementBeingVisible method.
     */
    public function testIWaitForCssElementBeingVisible()
    {
        $session = $this->createMock(Session::class);
        $session->expects($this->once())
            ->method('wait')
            ->with($this->equalTo(2000), $this->equalTo('$("foo").length >= 1 && $("foo").css("display") != "none"'))
            ->willReturn(true)
        ;

        $mock = $this->getExtraSessionMock();
        $mock->expects($this->once())->method('getSession')->willReturn($session);
        $mock->iWaitForCssElementBeingVisible('foo', 2);
    }

    /**
     * Tests the iWaitForCssElementBeingVisible FAIL method.
     *
     * @expectedException \RuntimeException
     */
    public function testIWaitForCssElementBeingVisibleFail()
    {
        $session = $this->createMock(Session::class);
        $session->expects($this->once())
            ->method('wait')
            ->with($this->equalTo(2000), $this->equalTo('$("foo").length >= 1 && $("foo").css("display") != "none"'))
            ->willReturn(false)
        ;

        $mock = $this->getExtraSessionMock();
        $mock->expects($this->once())->method('getSession')->willReturn($session);
        $mock->iWaitForCssElementBeingVisible('foo', 2);
    }

    /**
     * Tests the iWaitForCssElementBeingInvisible method.
     */
    public function testIWaitForCssElementBeingInvisible()
    {
        $session = $this->createMock(Session::class);
        $session->expects($this->once())
            ->method('wait')
            ->with($this->equalTo(2000), $this->equalTo('$("foo").length == false || $("foo").css("display") == "none"'))
            ->willReturn(true)
        ;

        $mock = $this->getExtraSessionMock();
        $mock->expects($this->once())->method('getSession')->willReturn($session);
        $mock->iWaitForCssElementBeingInvisible('foo', 2);
    }

    /**
     * Tests the iWaitForCssElementBeingInvisible FAIL method.
     *
     * @expectedException \RuntimeException
     */
    public function testIWaitForCssElementBeingInvisibleFail()
    {
        $session = $this->createMock(Session::class);
        $session->expects($this->once())
            ->method('wait')
            ->with($this->equalTo(2000), $this->equalTo('$("foo").length == false || $("foo").css("display") == "none"'))
            ->willReturn(false)
        ;

        $mock = $this->getExtraSessionMock();
        $mock->expects($this->once())->method('getSession')->willReturn($session);
        $mock->iWaitForCssElementBeingInvisible('foo', 2);
    }

    /**
     * Tests the scrollTo method.
     */
    public function testScrollTo()
    {
        $session = $this->createMock(Session::class);
        $session->expects($this->once())->method('executeScript')->with($this->equalTo('(function(){window.scrollTo(0, 10);})();'));

        $mock = $this->getExtraSessionMock();
        $mock->expects($this->once())->method('getSession')->willReturn($session);

        $mock->scrollTo(0, 10);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getExtraSessionMock()
    {
        return $this->getMockForTrait(
            ExtraSessionTrait::class,
            [],
            '',
            true,
            true,
            true,
            ['getSession']
        );
    }
}
