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

use Behat\Behat\Context\Environment\InitializedContextEnvironment;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Testwork\Suite\Suite;
use Ekino\BehatHelpers\BaseUrlTrait;
use PHPUnit\Framework\TestCase;

/**
 * @author Rémi Marseille <remi.marseille@ekino.com>
 */
class BaseUrlTraitTest extends TestCase
{
    /**
     * Tests the setBaseUrlBeforeScenario method.
     */
    public function testSetBaseUrlBeforeScenario(): void
    {
        $context = $this->createMock(RawMinkContext::class);
        $context->expects($this->once())->method('setMinkParameter')->with($this->equalTo('base_url'), $this->equalTo('https://foo.bar'));

        $env = new InitializedContextEnvironment($this->createMock(Suite::class));
        $env->registerContext($context);

        $scope = new BeforeScenarioScope($env, $this->createMock(FeatureNode::class), $this->createMock(ScenarioInterface::class));

        /** @var BaseUrlTrait $mock */
        $mock = $this->getMockForTrait(BaseUrlTrait::class);
        $mock->setBaseUrl('https://foo.bar');
        $mock->setBaseUrlBeforeScenario($scope);
    }
}
