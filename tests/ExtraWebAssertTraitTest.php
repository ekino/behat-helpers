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
use Behat\Mink\WebAssert;
use Ekino\BehatHelpers\ExtraWebAssertTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Rémi Marseille <remi.marseille@ekino.com>
 */
class ExtraWebAssertTraitTest extends TestCase
{
    /**
     * Tests the assertElementAttributeExists method.
     */
    public function testAssertElementAttributeExists(): void
    {
        $webAssert = $this->createMock(WebAssert::class);
        $webAssert->expects($this->once())->method('elementAttributeExists')->with($this->equalTo('css'), $this->equalTo('a.action_bar__next'));

        $trait = $this->getExtraWebAssertMock();
        $trait->expects($this->once())->method('assertSession')->willReturn($webAssert);
        $trait->expects($this->once())->method('fixStepArgument')->with($this->equalTo('disabled'));

        $trait->assertElementAttributeExists('a.action_bar__next', 'disabled'); // @phpstan-ignore-line
    }

    /**
     * Asserts the method clickElement throws an exception if element not found.
     */
    public function testClickElementThrowsExceptionIfElementNotFound(): void
    {
        $page = $this->createMock(DocumentElement::class);
        $page->expects($this->once())->method('find')->with($this->equalTo('css'), $this->equalTo('.sonata-ba-list a.sonata-link-identifier'));

        $session = $this->createMock(Session::class);
        $session->expects($this->once())->method('getPage')->willReturn($page);
        $session->expects($this->once())->method('getDriver')->willReturn($this->createMock(DriverInterface::class));

        $trait = $this->getExtraWebAssertMock();
        $trait->expects($this->exactly(2))->method('getSession')->willReturn($session);

        $this->expectException(ElementNotFoundException::class);

        $trait->clickElement('.sonata-ba-list a.sonata-link-identifier'); // @phpstan-ignore-line
    }

    /**
     * Tests the method clickElement.
     */
    public function testClickElement(): void
    {
        $element = $this->createMock(NodeElement::class);
        $element->expects($this->once())->method('click');

        $page = $this->createMock(DocumentElement::class);
        $page->expects($this->once())->method('find')->with($this->equalTo('css'), $this->equalTo('.sonata-ba-list a.sonata-link-identifier'))->willReturn($element);

        $session = $this->createMock(Session::class);
        $session->expects($this->once())->method('getPage')->willReturn($page);

        $trait = $this->getExtraWebAssertMock();
        $trait->expects($this->once())->method('getSession')->willReturn($session);

        $trait->clickElement('.sonata-ba-list a.sonata-link-identifier'); // @phpstan-ignore-line
    }

    /**
     * Tests the assertAtLeastNumElements method.
     */
    public function testAssertAtLeastNumElements(): void
    {
        $element = $this->createMock(NodeElement::class);

        $page = $this->createMock(DocumentElement::class);
        $page->expects($this->once())->method('findAll')->with($this->equalTo('css'), $this->equalTo('.foo'))->willReturn([$element, $element, $element]);

        $session = $this->createMock(Session::class);
        $session->expects($this->once())->method('getPage')->willReturn($page);

        $trait = $this->getExtraWebAssertMock();
        $trait->expects($this->once())->method('getSession')->willReturn($session);

        $trait->assertAtLeastNumElements(2, '.foo'); // @phpstan-ignore-line
    }

    /**
     * Tests the assertAtLeastNumElements method.
     */
    public function testAssertAtLeastNumElementsExactly(): void
    {
        $element = $this->createMock(NodeElement::class);

        $page = $this->createMock(DocumentElement::class);
        $page->expects($this->once())->method('findAll')->with($this->equalTo('css'), $this->equalTo('.foo'))->willReturn([$element, $element]);

        $session = $this->createMock(Session::class);
        $session->expects($this->once())->method('getPage')->willReturn($page);

        $trait = $this->getExtraWebAssertMock();
        $trait->expects($this->once())->method('getSession')->willReturn($session);

        $trait->assertAtLeastNumElements(2, '.foo'); // @phpstan-ignore-line
    }

    /**
     * Tests the assertAtLeastNumElements method.
     */
    public function testAssertAtLeastNumElementsNotEnough(): void
    {
        $element = $this->createMock(NodeElement::class);

        $page = $this->createMock(DocumentElement::class);
        $page->expects($this->once())->method('findAll')->with($this->equalTo('css'), $this->equalTo('.foo'))->willReturn($element);

        $session = $this->createMock(Session::class);
        $session->expects($this->once())->method('getPage')->willReturn($page);

        $trait = $this->getExtraWebAssertMock();
        $trait->expects($this->once())->method('getSession')->willReturn($session);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("1 \".foo\" found on the page, but should at least 2.");

        $trait->assertAtLeastNumElements(2, '.foo'); // @phpstan-ignore-line
    }

    /**
     * Tests the assertElementVisible method.
     */
    public function testAssertAtLeastNumElementsThrowsExceptionIfElementNotFound(): void
    {
        $page = $this->createMock(DocumentElement::class);
        $page->expects($this->once())->method('findAll')->with($this->equalTo('css'), $this->equalTo('.foo'));

        $session = $this->createMock(Session::class);
        $session->expects($this->once())->method('getPage')->willReturn($page);
        $session->expects($this->once())->method('getDriver')->willReturn($this->createMock(DriverInterface::class));

        $trait = $this->getExtraWebAssertMock();
        $trait->expects($this->exactly(2))->method('getSession')->willReturn($session);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Element matching css \".foo\" not found.");

        $trait->assertAtLeastNumElements(2, '.foo'); // @phpstan-ignore-line
    }

    /**
     * Tests the assertExactlyNumElement method.
     */
    public function testAssertExactlyNumElement(): void
    {
        $element = $this->createMock(NodeElement::class);

        $page = $this->createMock(DocumentElement::class);
        $page->expects($this->once())->method('findAll')->with($this->equalTo('css'), $this->equalTo('.foo'))->willReturn([$element, $element]);

        $session = $this->createMock(Session::class);
        $session->expects($this->once())->method('getPage')->willReturn($page);

        $trait = $this->getExtraWebAssertMock();
        $trait->expects($this->once())->method('getSession')->willReturn($session);

        $trait->assertExactlyNumElement(2, '.foo'); // @phpstan-ignore-line
    }

    /**
     * Tests the assertExactlyNumElement method.
     */
    public function testAssertExactlyNumElementNotEnough(): void
    {
        $element = $this->createMock(NodeElement::class);

        $page = $this->createMock(DocumentElement::class);
        $page->expects($this->once())->method('findAll')->with($this->equalTo('css'), $this->equalTo('.foo'))->willReturn($element);

        $session = $this->createMock(Session::class);
        $session->expects($this->once())->method('getPage')->willReturn($page);

        $trait = $this->getExtraWebAssertMock();
        $trait->expects($this->once())->method('getSession')->willReturn($session);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("1 \".foo\" found on the page, but should find 2.");

        $trait->assertExactlyNumElement(2, '.foo'); // @phpstan-ignore-line
    }

    /**
     * Tests the assertExactlyNumElement method.
     */
    public function testAssertExactlyNumElementTooMuch(): void
    {
        $element = $this->createMock(NodeElement::class);

        $page = $this->createMock(DocumentElement::class);
        $page->expects($this->once())->method('findAll')->with($this->equalTo('css'), $this->equalTo('.foo'))->willReturn([$element, $element, $element]);

        $session = $this->createMock(Session::class);
        $session->expects($this->once())->method('getPage')->willReturn($page);

        $trait = $this->getExtraWebAssertMock();
        $trait->expects($this->once())->method('getSession')->willReturn($session);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("3 \".foo\" found on the page, but should find 2.");

        $trait->assertExactlyNumElement(2, '.foo'); // @phpstan-ignore-line
    }

    /**
     * Tests the assertExactlyNumElement method.
     */
    public function testAssertExactlyNumElementThrowsExceptionIfElementNotFound(): void
    {
        $page = $this->createMock(DocumentElement::class);
        $page->expects($this->once())->method('findAll')->with($this->equalTo('css'), $this->equalTo('.foo'));

        $session = $this->createMock(Session::class);
        $session->expects($this->once())->method('getPage')->willReturn($page);
        $session->expects($this->once())->method('getDriver')->willReturn($this->createMock(DriverInterface::class));

        $trait = $this->getExtraWebAssertMock();
        $trait->expects($this->exactly(2))->method('getSession')->willReturn($session);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Element matching css \".foo\" not found.");

        $trait->assertExactlyNumElement(2, '.foo'); // @phpstan-ignore-line
    }

    /**
     * Tests the elementAttributeNotContains method.
     */
    public function testElementAttributeNotContains(): void
    {
        $webAssert = $this->createMock(WebAssert::class);
        $webAssert->expects($this->once())->method('elementAttributeNotContains')->with($this->equalTo('css'), $this->equalTo('foo'), $this->equalTo('bar'), $this->equalTo('value'));

        $trait = $this->getExtraWebAssertMock();
        $trait->expects($this->once())->method('assertSession')->willReturn($webAssert);

        $trait->elementAttributeNotContains('foo', 'bar', 'value'); // @phpstan-ignore-line
    }

    /**
     * @return MockObject
     */
    private function getExtraWebAssertMock(): MockObject
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
