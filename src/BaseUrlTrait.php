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
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    /**
     * @BeforeScenario
     *
     * @param BeforeScenarioScope $scope
     */
    public function setBaseUrlBeforeScenario(BeforeScenarioScope $scope)
    {
        foreach ($scope->getEnvironment()->getContexts() as $context) {
            if ($context instanceof RawMinkContext) {
                $context->setMinkParameter('base_url', $this->baseUrl);
            }
        }
    }
}
