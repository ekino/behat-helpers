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

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;
use Behat\MinkExtension\Context\RawMinkContext;
use Ekino\BehatHelpers\ExtraSessionTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Ekino\BehatHelpers\Traits\TestHelperTrait;

/**
 * @author Rémi Marseille <remi.marseille@ekino.com>
 * @author Benoit de Jacobet <benoit.de-jacobet@ekino.com>
 */
class ExtraSessionTraitTest extends TestCase
{
    use TestHelperTrait;

    /**
     * Tests the maximizeWindowOnBeforeScenario method.
     */
    public function testMaximizeWindowOnBeforeScenario(): void
    {
        $driver  = $this->createMock(DriverInterface::class);
        $session = $this->createMock(Session::class);
        $driver->expects($this->once())->method('maximizeWindow');
        $session->expects($this->once())->method('getDriver')->willReturn($driver);

        /** @var ExtraSessionTrait|MockObject $mock */
        $mock = $this->getExtraSessionMock();
        $mock->expects($this->once())->method('getSession')->willReturn($session);

        $mock->maximizeWindowOnBeforeScenario();
    }

    /**
     * Tests the scrollTo method.
     */
    public function testScrollTo(): void
    {
        $session = $this->createMock(Session::class);
        $session->expects($this->once())->method('executeScript')->with($this->equalTo('(function(){window.scrollTo(0, 10);})();'));

        /** @var ExtraSessionTrait|MockObject $mock */
        $mock = $this->getExtraSessionMock();
        $mock->expects($this->once())->method('getSession')->willReturn($session);

        $mock->scrollTo(0, 10);
    }

    /**
     * Tests the waitForSeconds method.
     */
    public function testWaitForSeconds(): void
    {
        $session = $this->createMock(Session::class);
        $session->expects($this->once())->method('wait')->with($this->equalTo(1000));

        /** @var ExtraSessionTrait|MockObject $mock */
        $mock = $this->getExtraSessionMock();
        $mock->expects($this->once())->method('getSession')->willReturn($session);

        $mock->waitForSeconds(1);
    }

    /**
     * Tests the iWaitForCssElementBeingVisible method.
     */
    public function testIWaitForCssElementBeingVisible(): void
    {
        $session = $this->createMock(Session::class);
        $session->expects($this->once())
            ->method('wait')
            ->with($this->equalTo(2000), $this->equalTo('$("foo").length >= 1 && $("foo").css("display") != "none"'))
            ->willReturn(true)
        ;

        /** @var ExtraSessionTrait|MockObject $mock */
        $mock = $this->getExtraSessionMock();
        $mock->expects($this->once())->method('getSession')->willReturn($session);
        $mock->iWaitForCssElementBeingVisible('foo', 2);
    }

    /**
     * Tests the iWaitForCssElementBeingVisible FAIL method.
     */
    public function testIWaitForCssElementBeingVisibleFail(): void
    {
        $this->expectException(\RuntimeException::class);
        $session = $this->createMock(Session::class);
        $session->expects($this->once())
            ->method('wait')
            ->with($this->equalTo(2000), $this->equalTo('$("foo").length >= 1 && $("foo").css("display") != "none"'))
            ->willReturn(false)
        ;

        /** @var ExtraSessionTrait|MockObject $mock */
        $mock = $this->getExtraSessionMock();
        $mock->expects($this->once())->method('getSession')->willReturn($session);
        $mock->iWaitForCssElementBeingVisible('foo', 2);
    }

    /**
     * Tests the iWaitForCssElementBeingInvisible method.
     */
    public function testIWaitForCssElementBeingInvisible(): void
    {
        $session = $this->createMock(Session::class);
        $session->expects($this->once())
            ->method('wait')
            ->with($this->equalTo(2000), $this->equalTo('$("foo").length == false || $("foo").css("display") == "none"'))
            ->willReturn(true)
        ;

        /** @var ExtraSessionTrait|MockObject $mock */
        $mock = $this->getExtraSessionMock();
        $mock->expects($this->once())->method('getSession')->willReturn($session);
        $mock->iWaitForCssElementBeingInvisible('foo', 2);
    }

    /**
     * Tests the iWaitForCssElementBeingInvisible FAIL method.
     */
    public function testIWaitForCssElementBeingInvisibleFail(): void
    {
        $this->expectException(\RuntimeException::class);
        $session = $this->createMock(Session::class);
        $session->expects($this->once())
            ->method('wait')
            ->with($this->equalTo(2000), $this->equalTo('$("foo").length == false || $("foo").css("display") == "none"'))
            ->willReturn(false)
        ;

        /** @var ExtraSessionTrait|MockObject $mock */
        $mock = $this->getExtraSessionMock();
        $mock->expects($this->once())->method('getSession')->willReturn($session);
        $mock->iWaitForCssElementBeingInvisible('foo', 2);
    }

    /**
     * Tests the method iWaitPageContains method.
     */
    public function testIWaitPageContains(): void
    {
        $page = $this->createMock(DocumentElement::class);
        $page->expects($this->once())
            ->method('waitFor')
            ->willReturn(true)
        ;

        $session = $this->createMock(Session::class);
        $session->expects($this->once())
            ->method('getPage')
            ->willReturn($page)
        ;

        /** @var ExtraSessionTrait|MockObject $mock */
        $mock = $this->getExtraSessionMock();
        $mock->expects($this->once())->method('getSession')->willReturn($session);
        $mock->iWaitPageContains(2, 'foo');
    }

    /**
     * Tests the method iWaitPageContains FAIL method.
     */
    public function testIWaitPageContainsFail(): void
    {
        $this->expectException(\RuntimeException::class);
        $page = $this->createMock(DocumentElement::class);
        $page->expects($this->once())
            ->method('waitFor')
            ->willReturn(false)
        ;

        $session = $this->createMock(Session::class);
        $session->expects($this->once())
            ->method('getPage')
            ->willReturn($page)
        ;

        /** @var ExtraSessionTrait|MockObject $mock */
        $mock = $this->getExtraSessionMock();
        $mock->expects($this->once())->method('getSession')->willReturn($session);
        $mock->iWaitPageContains(2, 'foo');
    }

    /**
     * Tests the method iWaitPageNotContains method.
     */
    public function testIWaitPageNotContains(): void
    {
        $page = $this->createMock(DocumentElement::class);
        $page->expects($this->once())
            ->method('waitFor')
            ->willReturn(true)
        ;

        $session = $this->createMock(Session::class);
        $session->expects($this->once())
            ->method('getPage')
            ->willReturn($page)
        ;

        /** @var ExtraSessionTrait|MockObject $mock */
        $mock = $this->getExtraSessionMock();
        $mock->expects($this->once())->method('getSession')->willReturn($session);
        $mock->iWaitPageNotContains(2, 'foo');
    }

    /**
     * Tests the method iWaitPageNotContains Fail method.
     */
    public function testIWaitPageNotContainsFail(): void
    {
        $this->expectException(\RuntimeException::class);
        $page = $this->createMock(DocumentElement::class);
        $page->expects($this->once())
            ->method('waitFor')
            ->willReturn(false)
        ;

        $session = $this->createMock(Session::class);
        $session->expects($this->once())
            ->method('getPage')
            ->willReturn($page)
        ;

        /** @var ExtraSessionTrait|MockObject $mock */
        $mock = $this->getExtraSessionMock();
        $mock->expects($this->once())->method('getSession')->willReturn($session);
        $mock->iWaitPageNotContains(2, 'foo');
    }

    /**
     * Asserts the method iClickOnText throws an exception if element not found.
     */
    public function testIClickOnTextThrowsExceptionIfElementNotFound(): void
    {
        $this->expectException(ElementNotFoundException::class);
        $this->expectExceptionMessage('Text matching xpath "foo" not found.');
        $page = $this->createMock(DocumentElement::class);
        $page->expects($this->once())->method('find')->with($this->equalTo('xpath'), $this->equalTo("//*[contains(.,'foo')]"));

        $session = $this->createMock(Session::class);
        $session->expects($this->once())->method('getPage')->willReturn($page);
        $session->expects($this->once())->method('getDriver')->willReturn($this->createMock(DriverInterface::class));

        /** @var ExtraSessionTrait|MockObject $mock */
        $mock = $this->getExtraSessionMock();
        $mock->expects($this->exactly(2))->method('getSession')->willReturn($session);

        $mock->iClickOnText('foo');
    }

    /**
     * Tests the method iClickOnCssElement.
     */
    public function testIClickOnText(): void
    {
        $element = $this->createMock(NodeElement::class);
        $element->expects($this->once())->method('click');

        $page = $this->createMock(DocumentElement::class);
        $page->expects($this->once())->method('find')->with($this->equalTo('xpath'), $this->equalTo("//*[contains(.,'foo')]"))->willReturn($element);

        $session = $this->createMock(Session::class);
        $session->expects($this->once())->method('getPage')->willReturn($page);

        /** @var ExtraSessionTrait|MockObject $mock */
        $mock = $this->getExtraSessionMock();
        $mock->expects($this->once())->method('getSession')->willReturn($session);

        $mock->iClickOnText('foo');
    }

    /**
     * @return MockObject
     */
    private function getExtraSessionMock(): MockObject
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
