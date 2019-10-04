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
use Ekino\BehatHelpers\SonataPageAdminTrait;
use phpmock\Mock;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Node\EmbedNode;

/**
 * @author William JEHANNE <william.jehanne@ekino.com>
 */
class SonataPageAdminTraitTest extends TestCase
{
    /**
     * @var Session|MockObject
     */
    private $session;

    /**
     * @var DocumentElement|MockObject
     */
    private $page;

    /**
     * @var NodeElement|MockObject
     */
    private $element;

    /**
     * @var NodeElement|MockObject
     */
    private $button;

    /**
     * @var DriverInterface|MockObject
     */
    private $driver;

    /**
     * @var WebAssert|MockObject
     */
    private $webAssert;

    /**
     * @var NodeElement|MockObject
     */
    private $block;

    /**
     * @var NodeElement|MockObject
     */
    private $tab;

    /**
     * @var NodeElement|MockObject
     */
    private $input;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->session   = $this->createMock(Session::class);
        $this->page      = $this->createMock(DocumentElement::class);
        $this->element   = $this->createMock(NodeElement::class);
        $this->button    = $this->createMock(NodeElement::class);
        $this->driver    = $this->createMock(DriverInterface::class);
        $this->webAssert = $this->createMock(WebAssert::class);
        $this->block     = $this->createMock(NodeElement::class);
        $this->tab       = $this->createMock(NodeElement::class);
        $this->input     = $this->createMock(NodeElement::class);
    }

    /**
     * Test iOpenTheContainerByText method with element found.
     */
    public function testIOpenTheContainerByText(): void
    {
        /** @var SonataPageAdminTrait|MockObject $trait */
        $trait = $this->getSonataPageAdminTraitMock();

        $this->element->expects($this->once())->method('click');

        $this->page->expects($this->once())
             ->method('find')
             ->with($this->equalTo('css'), $this->equalTo('div.page-composer__page-preview a:contains(\'Content\')'))
             ->willReturn($this->element)
        ;
        $this->session->expects($this->once())->method('getPage')->willReturn($this->page);
        $trait->expects($this->once())->method('getSession')->willReturn($this->session);

        $trait->iOpenTheContainerByText("Content");
    }

    /**
     * Test iOpenTheContainerByText method not found.
     */
    public function testIOpenTheContainerByTextNotFound(): void
    {
        /** @var SonataPageAdminTrait|MockObject $trait */
        $trait = $this->getSonataPageAdminTraitMock();

        $this->page->expects($this->once())
            ->method('find')
            ->with($this->equalTo('css'), $this->equalTo('div.page-composer__page-preview a:contains(\'Content\')'))
            ->willReturn(null)
        ;
        $this->session->expects($this->once())->method('getPage')->willReturn($this->page);
        $this->session->expects($this->once())->method('getDriver')->willReturn($this->driver);
        $trait->expects($this->exactly(2))->method('getSession')->willReturn($this->session);
        $this->expectExceptionMessage("Tag with text \"Content\" not found.");
        $this->expectException(ElementNotFoundException::class);

        $trait->iOpenTheContainerByText("Content");
    }

    /**
     * Test iAddABlockWithTheName method with element found.
     */
    public function testIAddABlockWithTheName(): void
    {
        /** @var SonataPageAdminTrait|MockObject $trait */
        $trait = $this->getSonataPageAdminTraitMock();

        $this->button->expects($this->once())->method('click');
        $this->page->expects($this->at(0))
            ->method('find')
            ->with($this->equalTo('css'), $this->equalTo('a.BlockSelectModal_SelectLink:contains(\'Simple text\')'))
            ->willReturn($this->button)
        ;

        $this->page->expects($this->at(1))
            ->method('find')
            ->with($this->equalTo('css'), $this->equalTo('li.page-composer__container__child:contains(\'Simple text\') * input.page-composer__container__child__name__input'))
            ->willReturn($this->input)
        ;

        $this->input->expects($this->once())->method('setValue');

        $this->session->expects($this->exactly(2))->method('getPage')->willReturn($this->page);
        $trait->expects($this->once())->method('thePopinShouldBeClosed')->with('blockSelectModal');
        $trait->expects($this->exactly(2))->method('getSession')->willReturn($this->session);
        $trait->expects($this->exactly(3))->method('iWaitForCssElementBeingVisible');
        $trait->expects($this->exactly(1))->method('clickingOnElementShouldOpenPopin')->with('div.page-composer__block-type-selector button', 'blockSelectModal');

        $trait->iAddABlockWithTheName("Simple text", "Foo");
    }

    /**
     * Test iAddABlockWithTheName method with button not found.
     */
    public function testIAddABlockWithTheNameButtonNotFound(): void
    {
        /** @var SonataPageAdminTrait|MockObject $trait */
        $trait = $this->getSonataPageAdminTraitMock();

        $trait->expects($this->at(0))
            ->method('iWaitForCssElementBeingVisible')
            ->with('div.page-composer__block-type-selector button', 2)
        ;

        $this->page->expects($this->at(0))
            ->method('find')
            ->with($this->equalTo('css'), $this->equalTo('a.BlockSelectModal_SelectLink:contains(\'Simple text\')'))
            ->willReturn(null)
        ;

        $this->session->expects($this->once())->method('getDriver')->willReturn($this->driver);
        $this->session->expects($this->once())->method('getPage')->willReturn($this->page);
        $trait->expects($this->exactly(2))->method('getSession')->willReturn($this->session);

        $this->expectExceptionMessage("Tag with block \"Simple text\" not found.");
        $this->expectException(ElementNotFoundException::class);

        $trait->iAddABlockWithTheName("Simple text", "Foo");
    }

    /**
     * Test iAddBlockWithTheName method with block not found.
     */
    public function testIAddBlockWithTheNameBlockNotFound(): void
    {
        /** @var SonataPageAdminTrait|MockObject $trait */
        $trait = $this->getSonataPageAdminTraitMock();

        $this->button->expects($this->once())->method('click');

        $this->page->expects($this->at(0))
            ->method('find')
            ->with($this->equalTo('css'), $this->equalTo('a.BlockSelectModal_SelectLink:contains(\'Simple text\')'))
            ->willReturn($this->button)
        ;

        $this->page->expects($this->at(1))
            ->method('find')
            ->with($this->equalTo('css'), $this->equalTo('li.page-composer__container__child:contains(\'Simple text\') * input.page-composer__container__child__name__input'))
            ->willReturn(null)
        ;

        $this->session->expects($this->exactly(1))->method('getDriver')->willReturn($this->driver);
        $this->session->expects($this->exactly(2))->method('getPage')->willReturn($this->page);
        $trait->expects($this->exactly(3))->method('getSession')->willReturn($this->session);
        $trait->expects($this->exactly(3))->method('iWaitForCssElementBeingVisible');
        $this->expectExceptionMessage("Tag with input \"Simple text\" not found.");
        $this->expectException(ElementNotFoundException::class);

        $trait->iAddABlockWithTheName("Simple text", "Foo");
    }

    /**
     * Test iAddBlockWithTheName method with input not found.
     */
    public function testIAddBlockWithTheNameInputNotFound(): void
    {
        /** @var SonataPageAdminTrait|MockObject $trait */
        $trait = $this->getSonataPageAdminTraitMock();

        $this->button->expects($this->once())->method('click');

        $this->page->expects($this->at(0))
            ->method('find')
            ->with($this->equalTo('css'), $this->equalTo('a.BlockSelectModal_SelectLink:contains(\'Simple text\')'))
            ->willReturn($this->button)
        ;

        $this->page->expects($this->at(1))
            ->method('find')
            ->with($this->equalTo('css'), $this->equalTo('li.page-composer__container__child:contains(\'Simple text\') * input.page-composer__container__child__name__input'))
            ->willReturn(null)
        ;

        $this->session->expects($this->exactly(1))->method('getDriver')->willReturn($this->driver);
        $this->session->expects($this->exactly(2))->method('getPage')->willReturn($this->page);
        $trait->expects($this->any())->method('getSession')->willReturn($this->session);
        $trait->expects($this->exactly(3))->method('iWaitForCssElementBeingVisible');
        $this->expectExceptionMessage("Tag with input \"Simple text\" not found.");
        $this->expectException(ElementNotFoundException::class);

        $trait->iAddABlockWithTheName("Simple text", "Foo");
    }

    /**
     * Test iGoToTheTabOfTheBlock method with element found.
     */
    public function testIGoToTheTabOfTheBlock(): void
    {
        /** @var SonataPageAdminTrait|MockObject $trait */
        $trait = $this->getSonataPageAdminTraitMock();

        $this->tab->expects($this->once())->method('click');
        $this->page->expects($this->once())->method('find')
             ->with($this->equalTo('css'), $this->equalTo('li.page-composer__container__child:contains(\'Foo\') * a:contains(\'English\')'))
             ->willReturn($this->tab)
        ;

        $this->session->expects($this->once())->method('getPage')->willReturn($this->page);
        $trait->expects($this->any())->method('getSession')->willReturn($this->session);

        $trait->iGoToTheTabOfTheBlock("English", "Foo");
    }

    /**
     * Test iGoToTheTabOfTheBlock method with element tab not found.
     */
    public function testIGoToTheTabOfTheBlockNotFound(): void
    {
        /** @var SonataPageAdminTrait|MockObject $trait */
        $trait = $this->getSonataPageAdminTraitMock();

        $this->tab->expects($this->never())->method('click');
        $this->page->expects($this->once())->method('find')
            ->with($this->equalTo('css'), $this->equalTo('li.page-composer__container__child:contains(\'Foo\') * a:contains(\'English\')'))
            ->willReturn(null)
        ;

        $this->session->expects($this->once())->method('getDriver')->willReturn($this->driver);
        $this->session->expects($this->once())->method('getPage')->willReturn($this->page);
        $trait->expects($this->any())->method('getSession')->willReturn($this->session);
        $this->expectExceptionMessage("Tag not found.");
        $this->expectException(ElementNotFoundException::class);

        $trait->iGoToTheTabOfTheBlock("English", "Foo");
    }

    /**
     * Test iShouldSeeBlocks method with element found.
     */
    public function testIShouldSeeBlocks(): void
    {
        /** @var SonataPageAdminTrait|MockObject $trait */
        $trait = $this->getSonataPageAdminTraitMock();

        $this->page->expects($this->once())->method('find')
             ->with($this->equalTo('css'), $this->equalTo('div.page-composer__container__view > ul.page-composer__container__children > li'))
             ->willReturn($this->element)
        ;

        $trait->expects($this->once())->method('assertNumElements')->with(1, 'div.page-composer__container__view > ul.page-composer__container__children > li');
        $this->session->expects($this->once())->method('getPage')->willReturn($this->page);
        $trait->expects($this->once())->method('getSession')->willReturn($this->session);

        $trait->iShouldSeeBlocks(1);
    }

    /**
     * Test iShouldSeeBlocks method with element not found.
     */
    public function testIShouldSeeBlocksElementNotFound(): void
    {
        /** @var SonataPageAdminTrait|MockObject $trait */
        $trait = $this->getSonataPageAdminTraitMock();

        $this->page->expects($this->once())->method('find')
            ->with($this->equalTo('css'), $this->equalTo('div.page-composer__container__view > ul.page-composer__container__children > li'))
            ->willReturn(null)
        ;

        $this->session->expects($this->once())->method('getDriver')->willReturn($this->driver);
        $this->session->expects($this->once())->method('getPage')->willReturn($this->page);
        $trait->expects($this->exactly(2))->method('getSession')->willReturn($this->session);
        $this->expectExceptionMessage("Tag not found.");
        $this->expectException(ElementNotFoundException::class);

        $trait->iShouldSeeBlocks(1);
    }

    /**
     * Test iOpenTheBlock method with element found.
     */
    public function testIOpenTheBlock(): void
    {
        /** @var SonataPageAdminTrait|MockObject $trait */
        $trait = $this->getSonataPageAdminTraitMock();

        $trait->expects($this->once())
            ->method('iWaitForCssElementBeingVisible')
            ->with($this->equalTo('li.page-composer__container__child:contains(\'Foo\') > a.page-composer__container__child__edit'), $this->equalTo(2))
        ;
        $this->block->expects($this->once())->method('click');
        $this->page->expects($this->once())->method('find')
            ->with($this->equalTo('css'), $this->equalTo('li.page-composer__container__child:contains(\'Foo\') > a.page-composer__container__child__edit'))
            ->willReturn($this->block)
        ;

        $this->session->expects($this->once())->method('getPage')->willReturn($this->page);
        $trait->expects($this->once())->method('getSession')->willReturn($this->session);

        $trait->iOpenTheBlock("Foo");
    }

    /**
     * Test iOpenTheBlock method with element not found.
     */
    public function testIOpenTheBlockNotFound(): void
    {
        /** @var SonataPageAdminTrait|MockObject $trait */
        $trait = $this->getSonataPageAdminTraitMock();

        $this->page->expects($this->once())->method('find')
            ->with($this->equalTo('css'), $this->equalTo('li.page-composer__container__child:contains(\'Foo\') > a.page-composer__container__child__edit'))
            ->willReturn(null)
        ;

        $this->session->expects($this->once())->method('getDriver')->willReturn($this->driver);
        $this->session->expects($this->once())->method('getPage')->willReturn($this->page);
        $trait->expects($this->exactly(2))->method('getSession')->willReturn($this->session);
        $this->expectExceptionMessage("Tag not found.");
        $this->expectException(ElementNotFoundException::class);

        $trait->iOpenTheBlock("Foo");
    }

    /**
     * Test iSubmitTheBlock method with element found.
     */
    public function testISubmitTheBlock(): void
    {
        /** @var SonataPageAdminTrait|MockObject $trait */
        $trait = $this->getSonataPageAdminTraitMock();

        $this->block->expects($this->once())->method('press');
        $this->page->expects($this->once())->method('find')
            ->with($this->equalTo('css'), $this->equalTo('li.page-composer__container__child:contains(\'Foo\') * button[type=submit]'))
            ->willReturn($this->block)
        ;

        $this->session->expects($this->once())->method('getPage')->willReturn($this->page);
        $trait->expects($this->once())->method('getSession')->willReturn($this->session);

        $trait->iSubmitTheBlock("Foo");
    }

    /**
     * Test iSubmitTheBlock method with element not found.
     */
    public function testISubmitTheBlockNotFound(): void
    {
        /** @var SonataPageAdminTrait|MockObject $trait */
        $trait = $this->getSonataPageAdminTraitMock();

        $this->page->expects($this->once())->method('find')
            ->with($this->equalTo('css'), $this->equalTo('li.page-composer__container__child:contains(\'Foo\') * button[type=submit]'))
            ->willReturn(null)
        ;

        $this->session->expects($this->once())->method('getDriver')->willReturn($this->driver);
        $this->session->expects($this->once())->method('getPage')->willReturn($this->page);
        $trait->expects($this->exactly(2))->method('getSession')->willReturn($this->session);
        $this->expectExceptionMessage("Tag not found.");
        $this->expectException(ElementNotFoundException::class);

        $trait->iSubmitTheBlock("Foo");
    }

    /**
     * Test iDeleteTheBlock method with element found.
     */
    public function testIDeleteTheBlock(): void
    {
        /** @var SonataPageAdminTrait|MockObject $trait */
        $trait = $this->getSonataPageAdminTraitMock();

        $this->page->expects($this->at(0))
            ->method('find')
            ->with($this->equalTo('css'), $this->equalTo('li:contains(\'Foo\')'))
            ->willReturn($this->element)
        ;
        $this->element->expects($this->once())->method('getAttribute')->with('data-block-id')->willReturn(1);
        $this->page->expects($this->at(1))
            ->method('find')
            ->with($this->equalTo('css'), $this->equalTo('li.page-composer__container__child:contains(\'Foo\') > a.page-composer__container__child__edit'))
            ->willReturn($this->element)
        ;

        $this->page->expects($this->at(2))
            ->method('find')
            ->with($this->equalTo('css'), $this->equalTo('li.page-composer__container__child[data-block-id=1] * a.btn-danger'))
            ->willReturn($this->button)
        ;
        $this->page->expects($this->at(3))
            ->method('find')
            ->with($this->equalTo('css'), $this->equalTo('button.btn-danger'))
            ->willReturn($this->button)
        ;

        $this->button->expects($this->once())->method('click');

        $this->element->expects($this->once())->method('getAttribute')->with('data-block-id')->willReturn(1);
        $this->session->expects($this->exactly(4))->method('getPage')->willReturn($this->page);
        $trait->expects($this->exactly(5))->method('getSession')->willReturn($this->session);

        $trait->iDeleteTheBlock("Foo");
    }

    /**
     * Test testIDeleteTheBlock method with element not found.
     */
    public function testIDeleteTheBlockElementNotFound(): void
    {
        /** @var SonataPageAdminTrait|MockObject $trait */
        $trait = $this->getSonataPageAdminTraitMock();

        $this->page->expects($this->at(0))
            ->method('find')
            ->with($this->equalTo('css'), $this->equalTo('li:contains(\'Foo\')'))
            ->willReturn(null)
        ;

        $this->expectExceptionMessage("Tag not found.");
        $this->expectException(ElementNotFoundException::class);
        $this->session->expects($this->once())->method('getDriver')->willReturn($this->driver);
        $this->session->expects($this->once())->method('getPage')->willReturn($this->page);
        $trait->expects($this->exactly(2))->method('getSession')->willReturn($this->session);
        $trait->iDeleteTheBlock("Foo");
    }

    /**
     * Test testIDeleteTheBlock method with button not found.
     */
    public function testIDeleteTheBlockButtonNotFound(): void
    {
        /** @var SonataPageAdminTrait|MockObject $trait */
        $trait = $this->getSonataPageAdminTraitMock();

        $this->page->expects($this->at(0))
            ->method('find')
            ->with($this->equalTo('css'), $this->equalTo('li:contains(\'Foo\')'))
            ->willReturn($this->element)
        ;
        $this->element->expects($this->once())->method('getAttribute')->with('data-block-id')->willReturn(1);
        $this->page->expects($this->at(1))->method('find')
            ->with($this->equalTo('css'), $this->equalTo('li.page-composer__container__child:contains(\'Foo\') > a.page-composer__container__child__edit'))
            ->willReturn($this->element)
        ;

        $this->page->expects($this->at(2))
            ->method('find')
            ->with($this->equalTo('css'), $this->equalTo('li.page-composer__container__child[data-block-id=1] * a.btn-danger'))
            ->willReturn(null)
        ;

        $this->expectExceptionMessage("Tag not found.");
        $this->expectException(ElementNotFoundException::class);
        $this->session->expects($this->once())->method('getDriver')->willReturn($this->driver);
        $this->session->expects($this->exactly(3))->method('getPage')->willReturn($this->page);
        $trait->expects($this->exactly(4))->method('getSession')->willReturn($this->session);

        $trait->iDeleteTheBlock("Foo");
    }

    /**
     * Test iRenameTheBlock method with element found.
     */
    public function testIRenameTheBlock(): void
    {
        /** @var SonataPageAdminTrait|MockObject $trait */
        $trait = $this->getSonataPageAdminTraitMock();

        $trait->expects($this->at(0))
            ->method('iWaitForCssElementBeingVisible')
            ->with('li.page-composer__container__child:contains(\'Foo\') > a.page-composer__container__child__edit', 2)
        ;

        $this->input->expects($this->once())->method('setValue')->with("Bar");
        $this->button->expects($this->once())->method('press');
        $this->element->expects($this->any())->method('getAttribute')->with('data-block-id')->willReturn(1);

        $this->page->expects($this->at(0))
            ->method('find')
            ->with($this->equalTo('css'), $this->equalTo('li.page-composer__container__child:contains(\'Foo\') > a.page-composer__container__child__edit'))
            ->willReturn($this->element)
        ;
        $this->page->expects($this->at(1))
            ->method('find')
            ->with($this->equalTo('css'), $this->equalTo('li:contains(\'Foo\')'))
            ->willReturn($this->element)
        ;
        $this->page->expects($this->at(2))
            ->method('find')
            ->with($this->equalTo('css'), $this->equalTo('li[data-block-id=1] * input.page-composer__container__child__name__input'))
            ->willReturn($this->input)
        ;
        $this->page->expects($this->at(3))
            ->method('find')
            ->with($this->equalTo('css'), $this->equalTo('li[data-block-id=1] * button[type=submit]'))
            ->willReturn($this->button)
        ;

        $this->session->expects($this->exactly(4))->method('getPage')->willReturn($this->page);
        $trait->expects($this->exactly(4))->method('getSession')->willReturn($this->session);
        $trait->iRenameTheBlock("Foo", "Bar");
    }

    /**
     * Test iRenameTheBlock method with element found.
     */
    public function testIRenameTheBlockInputNotFound(): void
    {
        /** @var SonataPageAdminTrait|MockObject $trait */
        $trait = $this->getSonataPageAdminTraitMock();

        $this->page->expects($this->at(0))
            ->method('find')
            ->with($this->equalTo('css'), $this->equalTo('li.page-composer__container__child:contains(\'Foo\') > a.page-composer__container__child__edit'))
            ->willReturn(null)
        ;

        $this->session->expects($this->once())->method('getDriver')->willReturn($this->driver);
        $this->session->expects($this->once())->method('getPage')->willReturn($this->page);
        $trait->expects($this->exactly(2))->method('getSession')->willReturn($this->session);
        $this->expectExceptionMessage("Tag not found.");
        $this->expectException(ElementNotFoundException::class);

        $trait->iRenameTheBlock("Foo", "Bar");
    }

    /**
     * Test theBlockShouldBeOpened method with element found.
     */
    public function testTheBlockShouldBeOpened(): void
    {
        /** @var SonataPageAdminTrait|MockObject $trait */
        $trait = $this->getSonataPageAdminTraitMock();

        $this->page->expects($this->once())
            ->method('find')
            ->with($this->equalTo('css'), $this->equalTo('li:contains(\'Foo\')'))
            ->willReturn($this->element)
        ;

        $this->element->expects($this->once())->method('getAttribute')->willReturn(1);
        $this->session->expects($this->once())->method('getPage')->willReturn($this->page);
        $trait->expects($this->once())->method('getSession')->willReturn($this->session);

        $this->webAssert->expects($this->once())
            ->method('elementContains')
            ->with($this->equalTo('css'), $this->equalTo('li.page-composer__container__child[data-block-id=1] > div.page-composer__container__child__content'), $this->equalTo('form'))
        ;

        $trait->expects($this->once())->method('assertSession')->willReturn($this->webAssert);

        $trait->theBlockShouldBeOpened("Foo");
    }

    /**
     * Test theBlockShouldBeOpened method with element not found.
     */
    public function testTheBlockShouldBeOpenedNotFound(): void
    {
        /** @var SonataPageAdminTrait|MockObject $trait */
        $trait = $this->getSonataPageAdminTraitMock();

        $this->page->expects($this->once())
            ->method('find')
            ->with($this->equalTo('css'), $this->equalTo('li:contains(\'Foo\')'))
            ->willReturn(null)
        ;

        $this->session->expects($this->once())->method('getDriver')->willReturn($this->driver);
        $this->session->expects($this->once())->method('getPage')->willReturn($this->page);
        $trait->expects($this->exactly(2))->method('getSession')->willReturn($this->session);
        $this->expectExceptionMessage("Tag not found.");
        $this->expectException(ElementNotFoundException::class);

        $trait->theBlockShouldBeOpened("Foo");
    }

    /**
     * Test theBlockShouldBeClosed method with element found.
     */
    public function testTheBlockShouldBeClosed(): void
    {
        /** @var SonataPageAdminTrait|MockObject $trait */
        $trait = $this->getSonataPageAdminTraitMock();

        $trait->expects($this->once())->method('iWaitForCssElementBeingVisible')->with('li.page-composer__container__child:contains(\'Foo\')');

        $this->page->expects($this->once())
            ->method('find')
            ->with($this->equalTo('css'), $this->equalTo('li.page-composer__container__child:contains(\'Foo\')'))
            ->willReturn($this->element)
        ;

        $trait->expects($this->once())
             ->method('elementAttributeNotContains')
             ->with($this->equalTo('li.page-composer__container__child:contains(\'Foo\')'), $this->equalTo('class'), $this->equalTo('page-composer__container__child--expanded'))
        ;

        $this->session->expects($this->once())->method('getPage')->willReturn($this->page);
        $trait->expects($this->once())->method('getSession')->willReturn($this->session);

        $trait->theBlockShouldBeClosed("Foo");
    }

    /**
     * Test theBlockShouldBeClosed method element not found.
     */
    public function testTheBlockShouldBeClosedNotFound(): void
    {
        /** @var SonataPageAdminTrait|MockObject $trait */
        $trait = $this->getSonataPageAdminTraitMock();

        $trait->expects($this->once())->method('iWaitForCssElementBeingVisible')->with($this->equalTo('li.page-composer__container__child:contains(\'Foo\')'));

        $this->page->expects($this->once())
            ->method('find')
            ->with($this->equalTo('css'), $this->equalTo('li.page-composer__container__child:contains(\'Foo\')'))
            ->willReturn(null)
        ;

        $this->session->expects($this->once())->method('getDriver')->willReturn($this->driver);
        $this->session->expects($this->once())->method('getPage')->willReturn($this->page);
        $trait->expects($this->exactly(2))->method('getSession')->willReturn($this->session);
        $this->expectExceptionMessage("Tag not found.");
        $this->expectException(ElementNotFoundException::class);

        $trait->theBlockShouldBeClosed("Foo");
    }

    /**
     * @return MockObject
     */
    private function getSonataPageAdminTraitMock(): MockObject
    {
        return $this->getMockForTrait(
            SonataPageAdminTrait::class,
            [],
            '',
            true,
            true,
            true,
            [
                'assertNumElements',
                'assertSession',
                'clickingOnElementShouldOpenPopin',
                'elementAttributeNotContains',
                'getAttribute',
                'getSession',
                'iWaitForCssElementBeingVisible',
                'setValue',
                'thePopinShouldBeOpened',
                'thePopinShouldBeClosed',
                'visit',
                'waitForSeconds'
            ]
        );
    }
}
