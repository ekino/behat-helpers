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

namespace Ekino\BehatHelpers;

use Behat\Mink\Exception\ElementNotFoundException;

/**
 * @author William JEHANNE <william.jehanne@ekino.com>
 */
trait SonataPageAdminTrait
{
    /**
     * I open the container by text.
     *
     * @When /^I open the container by text "([^"]*)"$/
     *
     * @param string $text
     *
     * @throws ElementNotFoundException
     */
    public function iOpenTheContainerByText(string $text): void
    {
        $element = $this->getSession()->getPage()->find('css', sprintf('div.page-composer__page-preview a:contains(\'%s\')', $text));

        if (null === $element) {
            throw new ElementNotFoundException($this->getSession()->getDriver(), null, 'text', $text);
        }

        $element->click();
    }

    /**
     * I add a block with the name.
     *
     * @When /^I add the block "([^"]*)" with the name "([^"]*)"$/
     *
     * @param string $text
     * @param string $name
     *
     * @throws ElementNotFoundException
     */
    public function iAddABlockWithTheName(string $text, string $name): void
    {
        $buttonElement = 'div.page-composer__block-type-selector button';
        $inputElement  = sprintf('li.page-composer__container__child:contains(\'%s\') * input.page-composer__container__child__name__input', $text);
        $blockElement  = sprintf('a.BlockSelectModal_SelectLink:contains(\'%s\')', $text);

        $this->iWaitForCssElementBeingVisible($buttonElement, 2);
        $this->clickingOnElementShouldOpenPopin($buttonElement, 'blockSelectModal');

        $this->iWaitForCssElementBeingVisible($blockElement, 2);

        $block = $this->getSession()->getPage()->find('css', $blockElement);

        if (null === $block) {
            throw new ElementNotFoundException($this->getSession()->getDriver(), null, 'block', $text);
        }

        $block->click();
        $this->thePopinShouldBeClosed('blockSelectModal');
        $this->iWaitForCssElementBeingVisible($inputElement, 2);

        $input = $this->getSession()->getPage()->find('css', $inputElement);

        if (null === $input) {
            throw new ElementNotFoundException($this->getSession()->getDriver(), null, 'input', $text);
        }

        $input->setValue($name);
    }

    /**
     * I go to the tab of the block.
     *
     * @When /^I go to the tab "([^"]*)" of the block "([^"]*)"$/
     *
     * @param string $tab
     * @param string $block
     *
     * @throws ElementNotFoundException
     */
    public function iGoToTheTabOfTheBlock(string $tab, string $block): void
    {
        $tab = $this->getSession()->getPage()->find('css', sprintf('li.page-composer__container__child:contains(\'%s\') * a:contains(\'%s\')', $block, $tab));

        if (null === $tab) {
            throw new ElementNotFoundException($this->getSession()->getDriver(), null, 'tab', $tab);
        }

        $tab->click();
    }

    /**
     * I should see n blocks.
     *
     * @When /^I should see (\d+) blocks$/
     *
     * @param int $num
     *
     * @throws ElementNotFoundException
     */
    public function iShouldSeeBlocks(int $num): void
    {
        $selector = 'div.page-composer__container__view > ul.page-composer__container__children > li';

        $element = $this->getSession()->getPage()->find('css', $selector);

        if (null === $element) {
            throw new ElementNotFoundException($this->getSession()->getDriver(), null, 'elements', $element);
        }

        $this->assertNumElements($num, $selector);
    }

    /**
     * I open the block.
     *
     * @When /^I open the block "([^"]*)"$/
     *
     * @param string $name
     *
     * @throws ElementNotFoundException
     */
    public function iOpenTheBlock(string $name): void
    {
        $element = sprintf('li.page-composer__container__child:contains(\'%s\') > a.page-composer__container__child__edit', $name);

        $this->iWaitForCssElementBeingVisible($element, 2);

        $block = $this->getSession()->getPage()->find('css', $element);

        if (null === $block) {
            throw new ElementNotFoundException($this->getSession()->getDriver(), null, 'block', $block);
        }

        $block->click();
    }

    /**
     * I submit the block.
     *
     * @When /^I submit the block "([^"]*)"$/
     *
     * @param string $name
     *
     * @throws ElementNotFoundException
     */
    public function iSubmitTheBlock(string $name): void
    {
        $button = $this->getSession()->getPage()->find('css', sprintf('li.page-composer__container__child:contains(\'%s\') * button[type=submit]', $name));

        if (null === $button) {
            throw new ElementNotFoundException($this->getSession()->getDriver(), null, 'block', $button);
        }

        $button->press();
    }

    /**
     * I delete the block.
     *
     * @When /^I delete the block "([^"]*)"$/
     *
     * @param string $name
     *
     * @throws ElementNotFoundException
     */
    public function iDeleteTheBlock(string $name): void
    {
        $block = $this->getSession()->getPage()->find('css', sprintf('li:contains(\'%s\')', $name));

        if (null === $block) {
            throw new ElementNotFoundException($this->getSession()->getDriver(), null, 'block', $block);
        }

        $id = $block->getAttribute('data-block-id');

        $this->iOpenTheBlock($name);

        $buttonElement = sprintf('li.page-composer__container__child[data-block-id=%d] * a.btn-danger', $id);
        $this->iWaitForCssElementBeingVisible($buttonElement, 1);
        $button        = $this->getSession()->getPage()->find('css', $buttonElement);

        if (null === $button) {
            throw new ElementNotFoundException($this->getSession()->getDriver(), null, 'button', $button);
        }

        $this->getSession()->executeScript(sprintf('window.scrollTo($(\'%s\').position().top+200, $(\'%s\').position().left+200);', $buttonElement, $buttonElement));

        $button->press();

        $btnDanger = 'button.btn-danger';

        $this->iWaitForCssElementBeingVisible($btnDanger, 1);

        $button = $this->getSession()->getPage()->find('css', $btnDanger);

        if (null === $button) {
            throw new ElementNotFoundException($this->getSession()->getDriver(), null, 'button', $button);
        }

        $button->click();
    }

    /**
     * I rename the block with.
     *
     * @When  /^I rename the block "([^"]*)" with "([^"]*)"$/
     *
     * @param string $oldName
     * @param string $newName
     *
     * @throws ElementNotFoundException
     */
    public function iRenameTheBlock(string $oldName, string $newName): void
    {
        $this->iOpenTheBlock($oldName);

        $block = $this->getSession()->getPage()->find('css', sprintf('li:contains(\'%s\')', $oldName));

        if (null === $block) {
            throw new ElementNotFoundException($this->getSession()->getDriver(), null, 'block', $block);
        }

        $id      = $block->getAttribute('data-block-id');
        $element = sprintf('li[data-block-id=%d] * input.page-composer__container__child__name__input', $id);

        $this->iWaitForCssElementBeingVisible($element, 2);

        $input = $this->getSession()->getPage()->find('css', $element);

        if (null === $input) {
            throw new ElementNotFoundException($this->getSession()->getDriver(), null, 'input', $block);
        }

        $input->setValue($newName);

        $button = $this->getSession()->getPage()->find('css', sprintf('li[data-block-id=%d] * button[type=submit]', $id));

        if (null === $button) {
            throw new ElementNotFoundException($this->getSession()->getDriver(), null, 'block', $button);
        }

        $button->press();
    }

    /**
     * The block should be opened.
     *
     * @When /^The block "([^"]*)" should be opened$/
     *
     * @param string $name
     *
     * @throws ElementNotFoundException
     */
    public function theBlockShouldBeOpened(string $name): void
    {
        $block = $this->getSession()->getPage()->find('css', sprintf('li:contains(\'%s\')', $name));

        if (null === $block) {
            throw new ElementNotFoundException($this->getSession()->getDriver(), null, 'block', $block);
        }

        $id      = $block->getAttribute('data-block-id');
        $element = sprintf('li.page-composer__container__child[data-block-id=%d] > div.page-composer__container__child__content', $id);

        $this->assertSession()->elementContains('css', $element, 'form');
    }

    /**
     * The block should be closed.
     *
     * @When /^The block "([^"]*)" should be closed$/
     *
     * @param string $name
     *
     * @throws ElementNotFoundException
     */
    public function theBlockShouldBeClosed(string $name): void
    {
        $element = sprintf('li.page-composer__container__child:contains(\'%s\')', $name);
        $block   = $this->getSession()->getPage()->find('css', $element);

        $this->iWaitForCssElementBeingVisible($element, 2);

        if (null === $block) {
            throw new ElementNotFoundException($this->getSession()->getDriver(), null, 'block', $block);
        }

        $this->elementAttributeNotContains($element, 'class', 'page-composer__container__child--expanded');
    }
}
