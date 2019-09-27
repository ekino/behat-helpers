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
 * @author Rémi Marseille <remi.marseille@ekino.com>
 * @author Benoit de Jacobet <benoit.de-jacobet@ekino.com>
 */
trait ExtraWebAssertTrait
{
    /**
     * Checks element has a specific attribute
     *
     * @Then /^the "(?P<element>[^"]*)" element should have attribute "(?P<value>(?:[^"]|\\")*)"$/
     *
     * @param string $element
     * @param string $value
     */
    public function assertElementAttributeExists(string $element, string $value): void
    {
        $this->assertSession()->elementAttributeExists('css', $element, $this->fixStepArgument($value));
    }

    /**
     * Checks that an attribute of a specific element does not contain text.
     *
     * @Then /^the attribute "(?P<attribute>(?:[^"]|\\")*)" of "(?P<element>[^"]*)" element should not have value "(?P<value>(?:[^"]|\\")*)"$/
     *
     * @param string $element
     * @param string $attribute
     * @param string $value
     */
    public function elementAttributeNotContains(string $element, string $attribute, string $value): void
    {
        $this->assertSession()->elementAttributeNotContains('css', $element, $attribute, $value);
    }

    /**
     * @When /^I click the "(?P<element>[^"]*)" element$/
     *
     * @param string $element
     *
     * @throws ElementNotFoundException
     */
    public function clickElement(string $element): void
    {
        $nodeElement = $this->getSession()->getPage()->find('css', $element);

        if (null === $nodeElement) {
            throw new ElementNotFoundException($this->getSession()->getDriver(), null, 'id|title|alt|text', $element);
        }

        $nodeElement->click();
    }

    /**
     * Checks at least X CSS elements exist on the page
     *
     * @Then /^(?:|I )should see at least (?P<num>\d+) "(?P<element>[^"]*)" elements?$/
     *
     * @param int    $num
     * @param string $selector
     *
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     * @throws \Exception
     */
    public function assertAtLeastNumElements(int $num, string $selector): void
    {
        $elements = $this->getSession()->getPage()->findAll('css', $selector);

        if (null === $elements) {
            throw new ElementNotFoundException($this->getSession()->getDriver(), 'element', 'css', $selector);
        }

        if (!\is_array($elements)) {
            $elements = [$elements];
        }

        if (\intval($num) > \count($elements)) {
            throw new \Exception(sprintf('%d "%s" found on the page, but should at least %d.', \count($elements), $selector, $num));
        }
    }

    /**
     * Checks exactly X CSS element exists on the page
     *
     * @Then /^(?:|I )should see exactly (?P<num>\d+) "(?P<element>[^"]*)" elements?$/
     *
     * @param int    $num
     * @param string $selector
     *
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     * @throws \Exception
     */
    public function assertExactlyNumElement(int $num, string $selector): void
    {
        $elements = $this->getSession()->getPage()->findAll('css', $selector);

        if (null === $elements) {
            throw new ElementNotFoundException($this->getSession()->getDriver(), 'element', 'css', $selector);
        }

        if (!\is_array($elements)) {
            $elements = [$elements];
        }

        if (\count($elements) !== \intval($num)) {
            throw new \Exception(sprintf('%d "%s" found on the page, but should find %d.', \count($elements), $selector, $num));
        }
    }
}
