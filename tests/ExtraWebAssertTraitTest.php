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
use Behat\Mink\WebAssert;
use Ekino\BehatHelpers\ExtraWebAssertTrait;
use PHPUnit\Framework\TestCase;

/**
 * @author Rémi Marseille <remi.marseille@ekino.com>
 */
class ExtraWebAssertTraitTest extends TestCase
{
    /**
     * Tests the assertElementAttributeExists method.
     */
    public function testAssertElementAttributeExists()
    {
        $webAssert = $this->createMock(WebAssert::class);
        $webAssert->expects($this->once())->method('elementAttributeExists')->with($this->equalTo('css'), $this->equalTo('a.action_bar__next'));

        $mock = $this->getExtraWebAssertMock();
        $mock->expects($this->once())->method('assertSession')->willReturn($webAssert);
        $mock->expects($this->once())->method('fixStepArgument')->with($this->equalTo('disabled'));

        $mock->assertElementAttributeExists('a.action_bar__next', 'disabled');
    }

    /**
     * Asserts the method clickElement throws an exception if element not found.
     *
     * @expectedException \Behat\Mink\Exception\ElementNotFoundException
     */
    public function testClickElementThrowsExceptionIfElementNotFound()
    {
        $page = $this->createMock(DocumentElement::class);
        $page->expects($this->once())->method('find')->with($this->equalTo('css'), $this->equalTo('.sonata-ba-list a.sonata-link-identifier'));

        $session = $this->createMock(Session::class);
        $session->expects($this->once())->method('getPage')->willReturn($page);
        $session->expects($this->once())->method('getDriver')->willReturn($this->createMock(DriverInterface::class));

        $mock = $this->getExtraWebAssertMock();
        $mock->expects($this->exactly(2))->method('getSession')->willReturn($session);

        $mock->clickElement('.sonata-ba-list a.sonata-link-identifier');
    }

    /**
     * Tests the method clickElement.
     */
    public function testClickElement()
    {
        $element = $this->createMock(NodeElement::class);
        $element->expects($this->once())->method('click');

        $page = $this->createMock(DocumentElement::class);
        $page->expects($this->once())->method('find')->with($this->equalTo('css'), $this->equalTo('.sonata-ba-list a.sonata-link-identifier'))->willReturn($element);

        $session = $this->createMock(Session::class);
        $session->expects($this->once())->method('getPage')->willReturn($page);

        $mock = $this->getExtraWebAssertMock();
        $mock->expects($this->once())->method('getSession')->willReturn($session);

        $mock->clickElement('.sonata-ba-list a.sonata-link-identifier');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getExtraWebAssertMock()
    {
        return $this->getMockForTrait(
            ExtraWebAssertTrait::class,
            [],
            '',
            true,
            true,
            true,
            ['assertSession', 'fixStepArgument', 'getSession']
        );
    }
}
