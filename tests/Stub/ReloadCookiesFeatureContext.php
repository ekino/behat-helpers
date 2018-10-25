<?php

/*
 * This file is part of the behat/helpers project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Ekino\BehatHelpers\Stub;

use Behat\MinkExtension\Context\MinkContext;
use Ekino\BehatHelpers\ReloadCookiesTrait;

/**
 * Class ReloadCookiesFeatureContext
 *
 * @author Quentin Belot <quentin.belot@ekino.com>
 */
class ReloadCookiesFeatureContext extends MinkContext
{
    use ReloadCookiesTrait;
}
