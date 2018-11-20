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
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Exception\ResponseTextException;
use Behat\MinkExtension\Context\MinkContext;

/**
 * @author Rémi Marseille <remi.marseille@ekino.com>
 * @author Benoit de Jacobet <benoit.de-jacobet@ekino.com>
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

    /**
     * Wait for the given css element being visible
     *
     * @param string $element
     *
     * @throws ExpectationException
     */
    public function iWaitForCssElementBeingVisible($element)
    {
        $this->spin(function (MinkContext $context) use ($element) {
            /** @var \Behat\Mink\Element\NodeElement[] $elements */
            $elements = $context->getSession()->getPage()->findAll('css', $element);
            if (count($elements) === 0 || $element === null) {
                return false;
            }

            foreach ($elements as $element) {
                if ($element->isVisible()) {
                    return true;
                }
            }

            return false;
        }, 'iWaitForCssElementBeingVisible::'.$element);
    }

    /**
     * Wait for the given css element being masked
     *
     * @param string $element
     *
     * @throws ExpectationException
     */
    public function iWaitForCssElementBeingInvisible($element)
    {
        $this->spin(function (MinkContext $context) use ($element) {
            $element = $context->getSession()->getPage()->find('css', $element);
            if ($element === null) {
                return false;
            }

            return !$element->isVisible();
        }, 'iWaitForCssElementBeingInvisible::'.$element);
    }

    /**
     * Wait for the page to contain given text
     *
     * @param string $text
     *
     * @throws ExpectationException
     */
    public function iWaitForPageContains($text)
    {
        $this->spin(function (MinkContext $context) use ($text) {
            // Assertion throw exception if not correct, nothing if correct
            try {
                $context->assertSession()->pageTextContains($text);

                return true;
            } catch (ResponseTextException $e) {
                return false;
            }
        }, 'iWaitForPageContains::'.$text);
    }

    /**
     * Wait for the page not to contain given text
     *
     * @param string $text
     *
     * @throws ExpectationException
     */
    public function iWaitForPageNotContains($text)
    {
        $this->spin(function (MinkContext $context) use ($text) {
            // Assertion throw exception if not correct, nothing if correct
            try {
                $context->assertSession()->pageTextNotContains($text);

                return true;
            } catch (ResponseTextException $e) {
                return false;
            }
        }, 'iWaitForPageNotContains::'.$text);
    }

    /**
     * Execute the given callback method until it returns true or timeout is reached
     *
     * @see http://docs.behat.org/en/v2.5/cookbook/using_spin_functions.html#adding-a-timeout
     *
     * @param callable $callback The method to execute
     * @param string   $message  A message to be displayed of callback fail
     * @param int      $wait     Time in second to try
     *
     * @throws ExpectationException
     */
    protected function spin($callback, $message, $wait = 60)
    {
        $time     = time();
        $stopTime = $time + $wait;

        while (time() < $stopTime) {
            try {
                if ($callback($this)) {
                    return;
                }
            } catch (\Exception $e) {
                // Do nothing
            }

            usleep(250000);
        }

        throw new ExpectationException(sprintf('Spin function timed out after %d seconds: %s', $wait, $message), $this->getSession());
    }
}
