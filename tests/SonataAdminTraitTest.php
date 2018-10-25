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
use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use Ekino\BehatHelpers\SonataAdminTrait;
use PHPUnit\Framework\TestCase;

/**
 * @author Rémi Marseille <remi.marseille@ekino.com>
 */
class SonataAdminTraitTest extends TestCase
{
    /**
     * Tests the login method.
     */
    public function testLogin()
    {
        $mock = $this->getSonataAdminMock();
        $mock->expects($this->once())->method('visitPath')->with($this->equalTo('sonata_user_admin_security_login'));
        $mock->expects($this->exactly(2))->method('fillField')->withConsecutive(
            [$this->equalTo('_username'), $this->equalTo('login')],
            [$this->equalTo('_password'), $this->equalTo('password')]
        );
        $mock->expects($this->once())->method('pressButton')->with($this->equalTo('Connexion'));

        $mock->login('login', 'password');
    }

    /**
     * Tests the iOpenMenuItemByText method with element found.
     */
    public function testIOpenMenuItemByText()
    {
        $mock    = $this->getSonataAdminMock();
        $session = $this->createMock(Session::class);
        $page    = $this->createMock(DocumentElement::class);
        $element = $this->createMock(NodeElement::class);

        $element->expects($this->once())->method('click');
        $page->expects($this->once())->method('find')->with('xpath', '//aside//span[text()="foo"]')->willReturn($element);
        $session->expects($this->once())->method('getPage')->willReturn($page);
        $mock->expects($this->once())->method('getSession')->willReturn($session);

        $mock->iOpenMenuItemByText('foo');
    }

    /**
     * Tests the iOpenMenuItemByText method with no element found.
     *
     * @expectedException Behat\Mink\Exception\ElementNotFoundException
     * @expectedExceptionMessage Tag with text "foo" not found
     */
    public function testIOpenMenuItemByTextWithElementNotFound()
    {
        $mock    = $this->getSonataAdminMock();
        $session = $this->createMock(Session::class);
        $page    = $this->createMock(DocumentElement::class);
        $driver  = $this->createMock(DriverInterface::class);

        $page->expects($this->once())->method('find')->with('xpath', '//aside//span[text()="foo"]');
        $session->expects($this->once())->method('getPage')->willReturn($page);
        $session->expects($this->once())->method('getDriver')->willReturn($driver);
        $mock->expects($this->exactly(2))->method('getSession')->willReturn($session);

        $mock->iOpenMenuItemByText('foo');
    }

    /**
     * Tests the iShouldSeeActionInNavbar method with element not found.
     *
     * @expectedException Behat\Mink\Exception\ElementNotFoundException
     * @expectedExceptionMessage Tag with text "foo" not found
     */
    public function testIShouldSeeActionInNavbarWithoutElementFound()
    {
        $driver  = $this->createMock(DriverInterface::class);
        $session = $this->createMock(Session::class);
        $page    = $this->createMock(DocumentElement::class);
        $mock    = $this->getSonataAdminMock();

        $page->expects($this->once())->method('find')->with($this->equalTo('xpath'), $this->equalTo('//nav//a[contains(.,"foo")]'));
        $session->expects($this->once())->method('getDriver')->willReturn($driver);
        $session->expects($this->once())->method('getPage')->willReturn($page);
        $mock->expects($this->exactly(2))->method('getSession')->willReturn($session);

        $mock->iShouldSeeActionInNavbar('foo');
    }

    /**
     * Tests the iShouldSeeActionInNavbar method with element not visible.
     *
     * @expectedException WebDriver\Exception\ElementNotVisible
     * @expectedExceptionMessage Cannot find action "foo" in Navbar action
     */
    public function testIShouldSeeActionInNavbarWithoutElementNotVisible()
    {
        $session = $this->createMock(Session::class);
        $page    = $this->createMock(DocumentElement::class);
        $element = $this->createMock(NodeElement::class);
        $mock    = $this->getSonataAdminMock();

        $element->expects($this->once())->method('isVisible')->willReturn(false);
        $page->expects($this->once())->method('find')->with($this->equalTo('xpath'), $this->equalTo('//nav//a[contains(.,"foo")]'))->willReturn($element);
        $session->expects($this->once())->method('getPage')->willReturn($page);
        $mock->expects($this->once())->method('getSession')->willReturn($session);

        $mock->iShouldSeeActionInNavbar('foo');
    }

    /**
     * Tests the iShouldNotSeeActionInNavbar method with element.
     *
     * @expectedException Behat\Mink\Exception\ElementHtmlException
     * @expectedExceptionMessage Action "foo" has been found in Navbar action
     */
    public function testIShouldNotSeeActionInNavbarWithElement()
    {
        $driver  = $this->createMock(DriverInterface::class);
        $session = $this->createMock(Session::class);
        $page    = $this->createMock(DocumentElement::class);
        $element = $this->createMock(NodeElement::class);
        $mock    = $this->getSonataAdminMock();

        $page->expects($this->once())->method('find')->with($this->equalTo('xpath'), $this->equalTo('//nav//a[contains(.,"foo")]'))->willReturn($element);
        $session->expects($this->once())->method('getDriver')->willReturn($driver);
        $session->expects($this->once())->method('getPage')->willReturn($page);
        $mock->expects($this->exactly(2))->method('getSession')->willReturn($session);

        $mock->iShouldNotSeeActionInNavbar('foo');
    }

    /**
     * Tests the iClickOnActionInNavbar method with element not found.
     *
     * @expectedException Behat\Mink\Exception\ElementNotFoundException
     * @expectedExceptionMessage Tag with text "foo" not found
     */
    public function testIClickOnActionInNavbarWithoutElementFound()
    {
        $driver  = $this->createMock(DriverInterface::class);
        $session = $this->createMock(Session::class);
        $page    = $this->createMock(DocumentElement::class);
        $mock    = $this->getSonataAdminMock();

        $page->expects($this->once())->method('find')->with($this->equalTo('xpath'), $this->equalTo('//nav//a[contains(.,"foo")]'));
        $session->expects($this->once())->method('getDriver')->willReturn($driver);
        $session->expects($this->once())->method('getPage')->willReturn($page);
        $mock->expects($this->exactly(2))->method('getSession')->willReturn($session);

        $mock->iClickOnActionInNavbar('foo');
    }

    /**
     * Tests the iClickOnActionInNavbar method with element found.
     */
    public function testIClickOnActionInNavbarWithElementFound()
    {
        $session = $this->createMock(Session::class);
        $page    = $this->createMock(DocumentElement::class);
        $element = $this->createMock(NodeElement::class);
        $mock    = $this->getSonataAdminMock();

        $element->expects($this->once())->method('click');
        $page->expects($this->once())->method('find')->with($this->equalTo('xpath'), $this->equalTo('//nav//a[contains(.,"foo")]'))->willReturn($element);
        $session->expects($this->once())->method('getPage')->willReturn($page);
        $mock->expects($this->once())->method('getSession')->willReturn($session);

        $mock->iClickOnActionInNavbar('foo');
    }

    /**
     * Tests the clickingOnElementShouldOpenPopin method.
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Please use the trait Ekino\BehatHelpers\ExtraSessionTrait in the class Trait_SonataAdminTrait
     */
    public function testClickingOnElementShouldOpenPopinWithoutExtraSessionTraitUse()
    {
        $mock = $this->getSonataAdminMock();
        $mock->clickingOnElementShouldOpenPopin('foo');
    }

    /**
     * Tests the thePopinShouldBeClosed method.
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Please use the trait Ekino\BehatHelpers\ExtraSessionTrait in the class Trait_SonataAdminTrait
     */
    public function testThePopinShouldBeClosedWithoutExtraSessionTraitUse()
    {
        $mock = $this->getSonataAdminMock();
        $mock->clickingOnElementShouldOpenPopin('foo');
    }

    /**
     * Tests the thePopinShouldNotBeOpened method with element found and visible.
     *
     * @expectedException Behat\Mink\Exception\ElementHtmlException
     * @expectedExceptionMessage Popin .modal-dialog was found and opened
     */
    public function testThePopinShouldNotBeOpenedWithOpenedPopin()
    {
        $driver  = $this->createMock(DriverInterface::class);
        $session = $this->createMock(Session::class);
        $page    = $this->createMock(DocumentElement::class);
        $element = $this->createMock(NodeElement::class);
        $mock    = $this->getSonataAdminMock();

        $element->expects($this->once())->method('isVisible')->willReturn(true);
        $page->expects($this->once())->method('find')->with($this->equalTo('css'), $this->equalTo('body > .modal > .modal-dialog'))->willReturn($element);
        $session->expects($this->once())->method('getPage')->willReturn($page);
        $session->expects($this->once())->method('getDriver')->willReturn($driver);
        $mock->expects($this->exactly(2))->method('getSession')->willReturn($session);

        $mock->thePopinShouldNotBeOpened();
    }

    /**
     * Tests the thePopinShouldNotBeOpened method without element found.
     */
    public function testThePopinShouldNotBeOpenedWithoutPopin()
    {
        $session = $this->createMock(Session::class);
        $page    = $this->createMock(DocumentElement::class);
        $mock    = $this->getSonataAdminMock();

        $page->expects($this->once())->method('find')->with($this->equalTo('css'), $this->equalTo('body > .modal > .modal-dialog'))->willReturn(null);
        $session->expects($this->once())->method('getPage')->willReturn($page);
        $mock->expects($this->once())->method('getSession')->willReturn($session);

        $mock->thePopinShouldNotBeOpened();
    }

    /**
     * Tests the thePopinShouldBeOpened method with element found and visible.
     */
    public function testThePopinShouldBeOpened()
    {
        $session = $this->createMock(Session::class);
        $page    = $this->createMock(DocumentElement::class);
        $element = $this->createMock(NodeElement::class);
        $mock    = $this->getSonataAdminMock();

        $element->expects($this->once())->method('isVisible')->willReturn(true);
        $page->expects($this->once())->method('find')->with($this->equalTo('css'), $this->equalTo('body > .modal > .modal-dialog'))->willReturn($element);
        $session->expects($this->once())->method('getPage')->willReturn($page);
        $mock->expects($this->once())->method('getSession')->willReturn($session);

        $mock->thePopinShouldBeOpened();
    }

    /**
     * Tests the thePopinShouldBeOpened method with element found and invisible.
     *
     * @expectedException WebDriver\Exception\ElementNotVisible
     * @expectedExceptionMessage Modal .modal-dialog should be opened and visible
     */
    public function testThePopinShouldBeOpenedWithInvisiblePopin()
    {
        $session = $this->createMock(Session::class);
        $page    = $this->createMock(DocumentElement::class);
        $element = $this->createMock(NodeElement::class);
        $mock    = $this->getSonataAdminMock();

        $element->expects($this->once())->method('isVisible')->willReturn(false);
        $page->expects($this->once())->method('find')->with($this->equalTo('css'), $this->equalTo('body > .modal > .modal-dialog'))->willReturn($element);
        $session->expects($this->once())->method('getPage')->willReturn($page);
        $mock->expects($this->once())->method('getSession')->willReturn($session);

        $mock->thePopinShouldBeOpened();
    }

    /**
     * Tests the thePopinShouldBeOpened method without element found.
     *
     * @expectedException WebDriver\Exception\ElementNotVisible
     * @expectedExceptionMessage Modal .modal-dialog should be opened and visible
     */
    public function testThePopinShouldBeOpenedWithoutPopin()
    {
        $session = $this->createMock(Session::class);
        $page    = $this->createMock(DocumentElement::class);
        $mock    = $this->getSonataAdminMock();

        $page->expects($this->once())->method('find')->with($this->equalTo('css'), $this->equalTo('body > .modal > .modal-dialog'))->willReturn(null);
        $session->expects($this->once())->method('getPage')->willReturn($page);
        $mock->expects($this->once())->method('getSession')->willReturn($session);

        $mock->thePopinShouldBeOpened();
    }

    /**
     * Tests the iFillInSelect2Field method.
     */
    public function testIFillInSelect2Field()
    {
        $session = $this->createMock(Session::class);
        $page    = $this->createMock(DocumentElement::class);
        $element = $this->createMock(NodeElement::class);
        $mock    = $this->getSonataAdminMock();

        $element->expects($this->once())->method('getAttribute')->with($this->equalTo('value'))->willReturn('foo');
        $page->expects($this->once())->method('find')->with($this->equalTo('xpath'), $this->equalTo(sprintf('//select[@id="%s"]//option[text()="%s"]', 'bar', 'foo')))->willReturn($element);
        $session->expects($this->once())->method('getPage')->willReturn($page);
        $session->expects($this->once())->method('executeScript')->with("jQuery('#bar').val([\"foo\"]).trigger('change');");
        $mock->expects($this->exactly(2))->method('getSession')->willReturn($session);

        $mock->iFillInSelect2Field('bar', 'foo');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getSonataAdminMock()
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
