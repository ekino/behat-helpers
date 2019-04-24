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

namespace Tests\Ekino\BehatHelpers\Stub;

/**
 * Mock the \WebDriver\Session class.
 *
 * @author Quentin Belot <quentin.belot@ekino.com>
 */
interface SessionMockInterface
{
    /**
     * @param string $cookie
     *
     * @return mixed
     */
    public function setCookie($cookie);

    /**
     * @return mixed
     */
    public function deleteAllCookies();
}
