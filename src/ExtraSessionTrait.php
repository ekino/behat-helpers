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

use Behat\Mink\Exception\ExpectationException;
use Behat\MinkExtension\Context\RawMinkContext;

/**
 * @author Rémi Marseille <remi.marseille@ekino.com>
 */
trait ExtraSessionTrait
{
    /**
     * @BeforeScenario
     */
    public function maximizeWindowOnBeforeScenario()
    {
        $this->getSession()->getDriver()->maximizeWindow();
    }

    /**
     * @When /^I wait for (\d+) seconds?$/
     *
     * @param int $seconds
     */
    public function waitForSeconds($seconds)
    {
        $this->getSession()->wait($seconds * 1000);
    }

    /**
     * Wait for the given css element being visible.
     *
     * @param string $element
     * @param int    $seconds
     *
     * @throws \RuntimeException
     *
     * @Given /^I wait for "([^"]*)" element being visible for (\d+) seconds$/
     */
    public function iWaitForCssElementBeingVisible($element, $seconds)
    {
        $result = $this->getSession()->wait($seconds * 1000, sprintf('$("%1$s").length >= 1 && $("%1$s").css("display") != "none"', $element));

        if (!$result) {
            throw new \RuntimeException(sprintf('Element "%s" not found', $element));
        }
    }

    /**
     * Wait for the given css element being masked.
     *
     * @param string $element
     * @param int    $seconds
     *
     * @throws \RuntimeException
     *
     * @Given /^I wait for "([^"]*)" element being invisible for (\d+) seconds$/
     */
    public function iWaitForCssElementBeingInvisible($element, $seconds)
    {
        $result = $this->getSession()->wait($seconds * 1000, sprintf('$("%1$s").length == false && $("%1$s").css("display") == "none"', $element));

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
    public function scrollTo($x, $y)
    {
        $this->getSession()->executeScript("(function(){window.scrollTo($x, $y);})();");
    }
}
