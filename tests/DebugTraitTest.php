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

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioNode;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Testwork\Environment\Environment;
use Behat\Testwork\Suite\Suite;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Stopwatch\Stopwatch;
use Tests\Ekino\BehatHelpers\Stub\DebugFeatureContext;
use Tests\Ekino\BehatHelpers\Traits\TestHelperTrait;

/**
 * @author Quentin Belot <quentin.belot@ekino.com>
 */
class DebugTraitTest extends TestCase
{
    use TestHelperTrait;

    /**
     * Tests the startProfilingBeforeScenario method.
     */
    public function testStartProfilingBeforeScenarioWithTag(): void
    {
        $env     = $this->createMock(Environment::class);
        $feature = $this->createMock(FeatureNode::class);
        $feature->expects($this->once())->method('getTags')->willReturn(['behat_helpers_profile']);

        $scenario = $this->createMock(ScenarioNode::class);
        $scenario->expects($this->once())->method('getTags')->willReturn([]);

        $scope   = new BeforeScenarioScope($env, $feature, $scenario);
        $context = new DebugFeatureContext();

        $context->startProfilingBeforeScenario($scope);
        $this->assertInstanceOf(Stopwatch::class, $this->getPrivatePropertyValue($context, 'stopwatch'));
    }

    /**
     * Tests the startProfilingBeforeScenario method.
     */
    public function testStartProfilingBeforeScenarioWithoutTag(): void
    {
        $env     = $this->createMock(Environment::class);
        $feature = $this->createMock(FeatureNode::class);
        $feature->expects($this->once())->method('getTags')->willReturn([]);

        $scenario = $this->createMock(ScenarioNode::class);
        $scenario->expects($this->once())->method('getTags')->willReturn([]);

        $scope   = new BeforeScenarioScope($env, $feature, $scenario);
        $context = new DebugFeatureContext();

        $context->startProfilingBeforeScenario($scope);
        $this->assertNull($this->getPrivatePropertyValue($context, 'stopwatch'));
    }

    /**
     * Tests the stopProfilingAfterScenario method.
     */
    public function testStopProfilingAfterScenarioWithTag(): void
    {
        $env     = $this->createMock(Environment::class);
        $feature = $this->createMock(FeatureNode::class);
        $feature->expects($this->once())->method('getTags')->willReturn(['behat_helpers_profile']);

        $scenario = $this->createMock(ScenarioNode::class);
        $scenario->expects($this->once())->method('getTags')->willReturn([]);

        $scope   = new BeforeScenarioScope($env, $feature, $scenario);
        $context = new DebugFeatureContext();

        $context->startProfilingBeforeScenario($scope);
        $this->assertInstanceOf(Stopwatch::class, $this->getPrivatePropertyValue($context, 'stopwatch'));
        $this->expectOutputRegex('#default(/scenario)?: (.*) MiB - (.*) ms#');
        $context->stopProfilingAfterScenario();
    }

    /**
     * Tests the stopProfilingAfterScenario method.
     */
    public function testStopProfilingAfterScenarioWithoutTag(): void
    {
        $env     = $this->createMock(Environment::class);
        $feature = $this->createMock(FeatureNode::class);
        $feature->expects($this->once())->method('getTags')->willReturn([]);

        $scenario = $this->createMock(ScenarioNode::class);
        $scenario->expects($this->once())->method('getTags')->willReturn([]);

        $scope   = new BeforeScenarioScope($env, $feature, $scenario);
        $context = new DebugFeatureContext();

        $context->startProfilingBeforeScenario($scope);
        $context->stopProfilingAfterScenario();
        $this->assertNull($this->getPrivatePropertyValue($context, 'stopwatch'));
    }
}
