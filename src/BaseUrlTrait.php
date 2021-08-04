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

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\MinkExtension\Context\RawMinkContext;

/**
 * @author Rémi Marseille <remi.marseille@ekino.com>
 */
trait BaseUrlTrait
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @param string $baseUrl
     *
     * @return self
     */
    public function setBaseUrl(string $baseUrl): self
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    /**
     * @BeforeScenario
     *
     * @param BeforeScenarioScope $scope
     */
    public function setBaseUrlBeforeScenario(BeforeScenarioScope $scope): void
    {
        /** @var \Behat\Behat\Context\Environment\InitializedContextEnvironment $env */
        $env = $scope->getEnvironment();

        foreach ($env->getContexts() as $context) {
            if ($context instanceof RawMinkContext) {
                $context->setMinkParameter('base_url', $this->baseUrl);
            }
        }
    }
}
