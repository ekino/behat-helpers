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
use Behat\Mink\Exception\ElementHtmlException;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;
use Ekino\BehatHelpers\SonataAdminTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WebDriver\Exception\ElementNotVisible;

/**
 * @author Rémi Marseille <remi.marseille@ekino.com>
 */
class SonataAdminTraitTest extends TestCase
{
    /**
     * Tests the login method.
     */
    public function testLogin(): void
    {
        $trait = $this->getSonataAdminMock();

        $trait->expects($this->once())->method('visitPath')->with($this->equalTo('sonata_user_admin_security_login'));
        $trait->expects($this->exactly(2))->method('fillField')->withConsecutive(
            [$this->equalTo('_username'), $this->equalTo('login')],
            [$this->equalTo('_password'), $this->equalTo('password')]
        );
        $trait->expects($this->once())->method('pressButton')->with($this->equalTo('Connexion'));

        $trait->login('login', 'password'); // @phpstan-ignore-line
    }

    /**
     * Tests the iOpenMenuItemByText method with element found.
     */
    public function testIOpenMenuItemByText(): void
    {
        $trait = $this->getSonataAdminMock();

        $session = $this->createMock(Session::class);
        $page    = $this->createMock(DocumentElement::class);
        $element = $this->createMock(NodeElement::class);

        $element->expects($this->once())->method('click');
        $page->expects($this->once())->method('find')->with('xpath', '//aside//span[text()="foo"]')->willReturn($element);
        $session->expects($this->once())->method('getPage')->willReturn($page);
        $trait->expects($this->once())->method('getSession')->willReturn($session);

        $trait->iOpenMenuItemByText('foo'); // @phpstan-ignore-line
    }

    /**
     * Tests the iOpenMenuItemByText method with no element found.
     */
    public function testIOpenMenuItemByTextWithElementNotFound(): void
    {
        $trait = $this->getSonataAdminMock();

        $session = $this->createMock(Session::class);
        $page    = $this->createMock(DocumentElement::class);
        $driver  = $this->createMock(DriverInterface::class);

        $page->expects($this->once())->method('find')->with('xpath', '//aside//span[text()="foo"]');
        $session->expects($this->once())->method('getPage')->willReturn($page);
        $session->expects($this->once())->method('getDriver')->willReturn($driver);
        $trait->expects($this->exactly(2))->method('getSession')->willReturn($session);

        $this->expectException(ElementNotFoundException::class);
        $this->expectExceptionMessage("Tag with text \"foo\" not found");

        $trait->iOpenMenuItemByText('foo'); // @phpstan-ignore-line
    }

    /**
     * Tests the iShouldSeeActionInNavbar method with element not found.
     */
    public function testIShouldSeeActionInNavbarWithoutElementFound(): void
    {
        $trait = $this->getSonataAdminMock();

        $driver  = $this->createMock(DriverInterface::class);
        $session = $this->createMock(Session::class);
        $page    = $this->createMock(DocumentElement::class);

        $page->expects($this->once())->method('find')->with($this->equalTo('xpath'), $this->equalTo('//nav//a[contains(.,"foo")]'));
        $session->expects($this->once())->method('getDriver')->willReturn($driver);
        $session->expects($this->once())->method('getPage')->willReturn($page);
        $trait->expects($this->exactly(2))->method('getSession')->willReturn($session);

        $this->expectException(ElementNotFoundException::class);
        $this->expectExceptionMessage("Tag with text \"foo\" not found");

        $trait->iShouldSeeActionInNavbar('foo'); // @phpstan-ignore-line
    }

    /**
     * Tests the iShouldSeeActionInNavbar method with element not visible.
     */
    public function testIShouldSeeActionInNavbarWithoutElementNotVisible(): void
    {
        $trait = $this->getSonataAdminMock();

        $session = $this->createMock(Session::class);
        $page    = $this->createMock(DocumentElement::class);
        $element = $this->createMock(NodeElement::class);

        $element->expects($this->once())->method('isVisible')->willReturn(false);
        $page->expects($this->once())->method('find')->with($this->equalTo('xpath'), $this->equalTo('//nav//a[contains(.,"foo")]'))->willReturn($element);
        $session->expects($this->once())->method('getPage')->willReturn($page);
        $trait->expects($this->once())->method('getSession')->willReturn($session);

        $this->expectException(ElementNotVisible::class);
        $this->expectExceptionMessage("Cannot find action \"foo\" in Navbar action");

        $trait->iShouldSeeActionInNavbar('foo'); // @phpstan-ignore-line
    }

    /**
     * Tests the iShouldNotSeeActionInNavbar method with element.
     */
    public function testIShouldNotSeeActionInNavbarWithElement(): void
    {
        $trait = $this->getSonataAdminMock();

        $driver  = $this->createMock(DriverInterface::class);
        $session = $this->createMock(Session::class);
        $page    = $this->createMock(DocumentElement::class);
        $element = $this->createMock(NodeElement::class);

        $page->expects($this->once())->method('find')->with($this->equalTo('xpath'), $this->equalTo('//nav//a[contains(.,"foo")]'))->willReturn($element);
        $session->expects($this->once())->method('getDriver')->willReturn($driver);
        $session->expects($this->once())->method('getPage')->willReturn($page);
        $trait->expects($this->exactly(2))->method('getSession')->willReturn($session);

        $this->expectException(ElementHtmlException::class);
        $this->expectExceptionMessage("Action \"foo\" has been found in Navbar action");

        $trait->iShouldNotSeeActionInNavbar('foo'); // @phpstan-ignore-line
    }

    /**
     * Tests the iClickOnActionInNavbar method with element not found.
     */
    public function testIClickOnActionInNavbarWithoutElementFound(): void
    {
        $trait = $this->getSonataAdminMock();

        $driver  = $this->createMock(DriverInterface::class);
        $session = $this->createMock(Session::class);
        $page    = $this->createMock(DocumentElement::class);

        $page->expects($this->once())->method('find')->with($this->equalTo('xpath'), $this->equalTo('//nav//a[contains(.,"foo")]'));
        $session->expects($this->once())->method('getDriver')->willReturn($driver);
        $session->expects($this->once())->method('getPage')->willReturn($page);
        $trait->expects($this->exactly(2))->method('getSession')->willReturn($session);

        $this->expectException(ElementNotFoundException::class);
        $this->expectExceptionMessage("Tag with text \"foo\" not found");

        $trait->iClickOnActionInNavbar('foo'); // @phpstan-ignore-line
    }

    /**
     * Tests the iClickOnActionInNavbar method with element found.
     */
    public function testIClickOnActionInNavbarWithElementFound(): void
    {
        $trait = $this->getSonataAdminMock();

        $session = $this->createMock(Session::class);
        $page    = $this->createMock(DocumentElement::class);
        $element = $this->createMock(NodeElement::class);

        $element->expects($this->once())->method('click');
        $page->expects($this->once())->method('find')->with($this->equalTo('xpath'), $this->equalTo('//nav//a[contains(.,"foo")]'))->willReturn($element);
        $session->expects($this->once())->method('getPage')->willReturn($page);
        $trait->expects($this->once())->method('getSession')->willReturn($session);

        $trait->iClickOnActionInNavbar('foo'); // @phpstan-ignore-line
    }

    /**
     * Tests the clickingOnElementShouldOpenPopin method.
     */
    public function testClickingOnElementShouldOpenPopinWithoutExtraSessionTraitUse(): void
    {
        $trait = $this->getSonataAdminMock();

        $this->expectExceptionMessage("Please use the trait Ekino\BehatHelpers\ExtraSessionTrait in the class Trait_SonataAdminTrait");
        $this->expectException(\RuntimeException::class);

        $trait->clickingOnElementShouldOpenPopin('foo', 'bar'); // @phpstan-ignore-line
    }

    /**
     * Tests the thePopinShouldBeClosed method.
     */
    public function testThePopinShouldBeClosedWithoutExtraSessionTraitUse(): void
    {
        $trait = $this->getSonataAdminMock();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Please use the trait Ekino\BehatHelpers\ExtraSessionTrait in the class Trait_SonataAdminTrait");

        $trait->clickingOnElementShouldOpenPopin('foo', 'bar'); // @phpstan-ignore-line
    }

    /**
     * Tests the thePopinShouldNotBeOpened method with element found and visible.
     */
    public function testThePopinShouldNotBeOpenedWithOpenedPopin(): void
    {
        $trait = $this->getSonataAdminMock();

        $driver  = $this->createMock(DriverInterface::class);
        $session = $this->createMock(Session::class);
        $page    = $this->createMock(DocumentElement::class);
        $element = $this->createMock(NodeElement::class);

        $element->expects($this->once())->method('isVisible')->willReturn(true);
        $page->expects($this->once())->method('find')->with($this->equalTo('css'), $this->equalTo('div.modal[id$=foo]'))->willReturn($element);
        $session->expects($this->once())->method('getPage')->willReturn($page);
        $session->expects($this->once())->method('getDriver')->willReturn($driver);
        $trait->expects($this->exactly(2))->method('getSession')->willReturn($session);

        $this->expectExceptionMessage("Popin div.modal[id$=foo] was found and opened");
        $this->expectException(ElementHtmlException::class);

        $trait->thePopinShouldNotBeOpened('foo'); // @phpstan-ignore-line
    }

    /**
     * Tests the thePopinShouldNotBeOpened method without element found.
     */
    public function testThePopinShouldNotBeOpenedWithoutPopin(): void
    {
        $trait = $this->getSonataAdminMock();

        $session = $this->createMock(Session::class);
        $page    = $this->createMock(DocumentElement::class);

        $page->expects($this->once())->method('find')->with($this->equalTo('css'), $this->equalTo('div.modal[id$=foo]'))->willReturn(null);
        $session->expects($this->once())->method('getPage')->willReturn($page);
        $trait->expects($this->once())->method('getSession')->willReturn($session);

        $trait->thePopinShouldNotBeOpened('foo'); // @phpstan-ignore-line
    }

    /**
     * Tests the thePopinShouldBeOpened method with element found and visible.
     */
    public function testThePopinShouldBeOpened(): void
    {
        $trait = $this->getSonataAdminMock();

        $session = $this->createMock(Session::class);
        $page    = $this->createMock(DocumentElement::class);
        $element = $this->createMock(NodeElement::class);

        $element->expects($this->once())->method('isVisible')->willReturn(true);
        $page->expects($this->once())->method('find')->with($this->equalTo('css'), $this->equalTo('div.modal[id$=foo]'))->willReturn($element);
        $session->expects($this->once())->method('getPage')->willReturn($page);
        $trait->expects($this->once())->method('getSession')->willReturn($session);

        $trait->thePopinShouldBeOpened('foo'); // @phpstan-ignore-line
    }

    /**
     * Tests the thePopinShouldBeOpened method with element found and invisible.
     */
    public function testThePopinShouldBeOpenedWithInvisiblePopin(): void
    {
        $trait = $this->getSonataAdminMock();

        $session = $this->createMock(Session::class);
        $page    = $this->createMock(DocumentElement::class);
        $element = $this->createMock(NodeElement::class);

        $element->expects($this->once())->method('isVisible')->willReturn(false);
        $page->expects($this->once())->method('find')->with($this->equalTo('css'), $this->equalTo('div.modal[id$=foo]'))->willReturn($element);
        $session->expects($this->once())->method('getPage')->willReturn($page);
        $trait->expects($this->once())->method('getSession')->willReturn($session);

        $this->expectExceptionMessage("Modal div.modal[id$=foo] should be opened and visible");
        $this->expectException(ElementNotVisible::class);

        $trait->thePopinShouldBeOpened('foo'); // @phpstan-ignore-line
    }

    /**
     * Tests the thePopinShouldBeOpened method without element found.
     */
    public function testThePopinShouldBeOpenedWithoutPopin(): void
    {
        $trait = $this->getSonataAdminMock();

        $session = $this->createMock(Session::class);
        $page    = $this->createMock(DocumentElement::class);

        $page->expects($this->once())->method('find')->with($this->equalTo('css'), $this->equalTo('div.modal[id$=foo]'))->willReturn(null);
        $session->expects($this->once())->method('getPage')->willReturn($page);
        $trait->expects($this->once())->method('getSession')->willReturn($session);

        $this->expectExceptionMessage("Modal div.modal[id$=foo] should be opened and visible");
        $this->expectException(ElementNotVisible::class);

        $trait->thePopinShouldBeOpened('foo'); // @phpstan-ignore-line
    }

    /**
     * Tests the iFillInSelect2Field method.
     */
    public function testIFillInSelect2Field(): void
    {
        $trait = $this->getSonataAdminMock();

        $session = $this->createMock(Session::class);
        $page    = $this->createMock(DocumentElement::class);
        $element = $this->createMock(NodeElement::class);

        $element->expects($this->once())->method('getAttribute')->with($this->equalTo('value'))->willReturn('foo');
        $page->expects($this->once())->method('find')->with($this->equalTo('xpath'), $this->equalTo(sprintf('//select[@id="%s"]//option[text()="%s"]', 'bar', 'foo')))->willReturn($element);
        $session->expects($this->once())->method('getPage')->willReturn($page);
        $session->expects($this->once())->method('executeScript')->with("jQuery('#bar').val([\"foo\"]).trigger('change');");
        $trait->expects($this->exactly(2))->method('getSession')->willReturn($session);

        $trait->iFillInSelect2Field('bar', 'foo'); // @phpstan-ignore-line
    }

    /**
     * @return MockObject
     */
    private function getSonataAdminMock(): MockObject
    {
        return $this->getMockForTrait(
            SonataAdminTrait::class,
            [],
            '',
            true,
            true,
            true,
            ['fillField', 'getSession', 'pressButton', 'visitPath']
        );
    }
}
