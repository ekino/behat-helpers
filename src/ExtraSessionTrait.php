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
use Behat\Mink\Exception\ResponseTextException;
use Behat\MinkExtension\Context\MinkContext;

/**
 * @author Rémi Marseille <remi.marseille@ekino.com>
 * @author Benoit de Jacobet <benoit.de-jacobet@ekino.com>
 */
trait ExtraSessionTrait
{
    /**
     * @BeforeScenario
     */
    public function maximizeWindowOnBeforeScenario(): void
    {
        $this->getSession()->getDriver()->maximizeWindow();
    }

    /**
     * @When /^I wait for (\d+) seconds?$/
     *
     * @param int $seconds
     */
    public function waitForSeconds(int $seconds): void
    {
        $this->getSession()->wait($seconds * 1000);
    }

    /**
     * Wait for the given css element being visible.
     *
     * @Given /^I wait for "([^"]*)" element being visible for (\d+) seconds$/
     *
     * @param string $element
     * @param int    $seconds
     *
     * @throws \RuntimeException
     */
    public function iWaitForCssElementBeingVisible(string $element, int $seconds): void
    {
        $result = $this->getSession()->wait($seconds * 1000, sprintf('$("%1$s").length >= 1 && $("%1$s").css("display") != "none"', $element));

        if (!$result) {
            throw new \RuntimeException(sprintf('Element "%s" not found', $element));
        }
    }

    /**
     * Wait for the given css element being masked
     *
     * @Given /^I wait for "([^"]*)" element being invisible for (\d+) seconds$/
     *
     * @param string $element
     * @param int    $seconds
     *
     * @throws \RuntimeException
     */
    public function iWaitForCssElementBeingInvisible(string $element, int $seconds): void
    {
        $result = $this->getSession()->wait($seconds * 1000, sprintf('$("%1$s").length == false || $("%1$s").css("display") == "none"', $element));

        if (!$result) {
            throw new \RuntimeException(sprintf('Element "%s" did not disappear', $element));
        }
    }

    /**
     * @When /^I scroll to (\d+) and (\d+)?$/
     *
     * @param int $x
     * @param int $y
     */
    public function scrollTo(int $x, int $y): void
    {
        $this->getSession()->executeScript("(function(){window.scrollTo($x, $y);})();");
    }

    /**
     * Wait the page contains given text
     *
     * @Given /^I wait (\d+) seconds that page contains text "([^"]*)"$/
     *
     * @param string $text
     * @param int    $seconds
     *
     * @throws \RuntimeException
     */
    public function iWaitPageContains($text, $seconds): void
    {
        $page = $this->getSession()->getPage();

        $result = $page->waitFor($seconds, function (MinkContext $context) use ($text) {
            // Assertion throw exception if not correct, nothing if correct
            try {
                $context->assertSession()->pageTextContains($text);

                return true;
            } catch (ResponseTextException $e) {
                return false;
            }
        });

        if (!$result) {
            throw new \RuntimeException(sprintf('page not contains text : "%s"', $text));
        }
    }

    /**
     * Wait until the page not contains given text
     *
     * @Given /^I wait (\d+) seconds that page not contains text "([^"]*)"$/
     *
     * @param string $text
     * @param int    $seconds
     *
     * @throws \RuntimeException
     */
    public function iWaitPageNotContains($text, $seconds): void
    {
        $page = $this->getSession()->getPage();

        $result = $page->waitFor($seconds, function (MinkContext $context) use ($text) {
            // Assertion throw exception if not correct, nothing if correct
            try {
                $context->assertSession()->pageTextNotContains($text);

                return true;
            } catch (ResponseTextException $e) {
                return false;
            }
        });

        if (!$result) {
            throw new \RuntimeException(sprintf('page contains text : "%s"', $text));
        }
    }

    /**
     * Click on the matching text
     *
     * @Given /^I click on (?:link|button) containing "(?P<text>[^"]*)"$/
     *
     * @param string $text
     *
     * @throws ElementNotFoundException
     */
    public function iClickOnText($text): void
    {
        $page    = $this->getSession()->getPage();
        $element = $page->find('xpath', sprintf("//*[contains(.,'%s')]", $text));

        if (null === $element) {
            throw new ElementNotFoundException($this->getSession()->getDriver(), 'text', 'xpath', $text);
        }

        $element->click();
    }
}
