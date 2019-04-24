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

use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Behat\Testwork\Tester\Result\TestResult;
use Cocur\Slugify\Slugify;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * @author Raphaël Benitte <benitte@ekino.com>
 * @author Benoit Mazière <benoit.maziere@ekino.com>
 * @author Rémi Marseille <remi.marseille@ekino.com>
 */
trait DebugTrait
{
    /**
     * @var Stopwatch
     */
    private $stopwatch;

    /**
     * @BeforeScenario
     *
     * @param BeforeScenarioScope $scope
     */
    public function startProfilingBeforeScenario(BeforeScenarioScope $scope): void
    {
        if (!\in_array('behat_helpers_profile', array_merge($scope->getFeature()->getTags(), $scope->getScenario()->getTags()))) {
            return;
        }

        $this->stopwatch = new Stopwatch();
        $this->stopwatch->start('scenario');
    }

    /**
     * @AfterScenario
     */
    public function stopProfilingAfterScenario(): void
    {
        if (empty($this->stopwatch)) {
            return;
        }

        echo $this->stopwatch->stop('scenario');
    }

    /**
     * Extract screenshot and content from failed step.
     *
     * @AfterStep
     *
     * @param AfterStepScope $scope
     *
     * @throws \Behat\Mink\Exception\DriverException
     * @throws \Behat\Mink\Exception\UnsupportedDriverActionException
     * @throws \RuntimeException
     */
    public function collectDebugAfterFailedStep(AfterStepScope $scope): void
    {
        if (TestResult::FAILED !== $scope->getTestResult()->getResultCode()) {
            return;
        }

        if (!$this instanceof RawMinkContext) {
            return;
        }

        $driver = $this->getSession()->getDriver();

        if (!$driver instanceof Selenium2Driver) {
            return;
        }

        if (!\in_array(KernelDictionary::class, class_uses($this))) {
            throw new \RuntimeException(sprintf('Please use the trait %s in the class %s', KernelDictionary::class, __CLASS__));
        }

        $slugify = new Slugify();

        $path = sprintf('%s/%s.%s',
            $this->getContainer()->getParameter('kernel.logs_dir'),
            $slugify->slugify($scope->getFeature()->getTitle() ?? ''),
            $slugify->slugify($scope->getStep()->getText())
        );

        file_put_contents(sprintf('%s.html', $path), $driver->getContent());
        echo sprintf("saved failed step content to %s.html\n", $path);

        file_put_contents(sprintf('%s.png', $path), $driver->getScreenshot());
        echo sprintf("saved failed step screenshot to %s.png", $path);
    }
}
