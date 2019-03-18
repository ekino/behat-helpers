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

use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Mink\Driver\Selenium2Driver;

/**
 * @author Rémi Marseille <remi.marseille@ekino.com>
 */
trait ReloadCookiesTrait
{
    /**
     * @var array
     */
    private static $steps = [];

    /**
     * @var array
     */
    private static $cookies = [];

    /**
     * @var bool
     */
    private $saveCookies = false;

    /**
     * @var bool
     */
    private $cookiesReloaded = false;

    /**
     * @var string[]
     */
    private $tags;

    /**
     * @BeforeScenario
     *
     * @param BeforeScenarioScope $scope
     */
    public function getTagsBeforeScenario(BeforeScenarioScope $scope): void
    {
        $this->tags = array_merge($scope->getFeature()->getTags(), $scope->getScenario()->getTags());
    }

    /**
     * @AfterScenario
     *
     * @param AfterScenarioScope $scope
     */
    public function saveCookiesAfterScenario(AfterScenarioScope $scope): void
    {
        if (!$this->saveCookies) {
            return;
        }

        $driver = $this->assertDriverSupported();

        echo "Saving cookies...\n";

        static::$cookies = $driver->getWebDriverSession()->getAllCookies();
    }

    /**
     * @param callable $callback
     */
    public function doOnce(callable $callback): void
    {
        $reset     = \in_array('behat_helpers_reset_cache', $this->tags);
        $cacheable = !\in_array('behat_helpers_no_cache', $this->tags);
        $bt        = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller    = $bt[1]['function'];

        if ($reset) {
            $this->resetCookies();
        }

        if (!$this->cookiesReloaded && !$reset && $cacheable) {
            $this->reloadCookies();

            $this->cookiesReloaded = true;
        }

        if ($cacheable) {
            if (!\in_array($caller, static::$steps)) {
                \call_user_func($callback);

                static::$steps[] = $caller;

                $this->saveCookies = true;
            }
        } else {
            \call_user_func($callback);
        }
    }

    /**
     * Reset cookies
     *
     * @return bool
     */
    private function resetCookies(): bool
    {
        if (!static::$cookies) {
            return false;
        }

        static::$cookies = [];
        static::$steps   = [];

        $driver = $this->assertDriverSupported();

        echo "Resetting cookies...\n";

        $session = $driver->getWebDriverSession();
        $session->deleteAllCookies();

        return true;
    }

    /**
     * Reloads previous cookies to avoid redoing many times the same job
     *
     * @return bool
     */
    private function reloadCookies(): bool
    {
        if (!static::$cookies) {
            return false;
        }

        $driver = $this->assertDriverSupported();

        echo "Reloading cookies...\n";

        $session = $driver->getWebDriverSession();

        foreach (static::$cookies as $cookie) {
            $session->setCookie($cookie);
        }

        return true;
    }

    /**
     * @throws \RuntimeException
     * @return Selenium2Driver
     *
     */
    private function assertDriverSupported(): Selenium2Driver
    {
        $driver = $this->getSession()->getDriver();

        if (!$driver instanceof Selenium2Driver) {
            throw new \RuntimeException(sprintf('Saving cookies only works with driver %s', Selenium2Driver::class));
        }

        return $driver;
    }
}
