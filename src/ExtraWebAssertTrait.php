<?php

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
 */
trait ExtraWebAssertTrait
{
    /**
     * @Then /^the "(?P<element>[^"]*)" element should have attribute "(?P<value>(?:[^"]|\\")*)"$/
     *
     * @param string $element
     * @param string $value
     */
    public function assertElementAttributeExists($element, $value)
    {
        $this->assertSession()->elementAttributeExists('css', $element, $this->fixStepArgument($value));
    }

    /**
     * @When /^I click the "(?P<element>[^"]*)" element$/
     *
     * @param string $element
     *
     * @throws ElementNotFoundException
     */
    public function clickElement($element)
    {
        $nodeElement = $this->getSession()->getPage()->find('css', $element);

        if (null === $nodeElement) {
            throw new ElementNotFoundException($this->getSession()->getDriver(), null, 'id|title|alt|text', $element);
        }

        $nodeElement->click();
    }
}
