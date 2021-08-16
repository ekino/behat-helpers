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

use Ekino\BehatHelpers\RouterAwareTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Ekino\BehatHelpers\Traits\TestHelperTrait;

class RouterAwareTraitTest extends TestCase
{
    use TestHelperTrait;

    /**
     * Tests the removeHost method.
     *
     * @dataProvider getUrls
     */
    public function testRemoveHost(string $url, string $result): void
    {
        $trait = $this->getRouterAwareTraitMock();

        $this->assertSame($result, $this->invokeMethod($trait, 'removeHost', [$url]));
    }

    /**
     * @return \Generator<array<string>>
     */
    public function getUrls(): \Generator
    {
        yield 'case_1'  => ['http://host.com?foo', '?foo'];
        yield 'case_2'  => ['http://host.com?foo', '?foo'];
        yield 'case_3'  => ['http://host.com/foo', '/foo'];
        yield 'case_4'  => ['https://host.com/foo', '/foo'];
        yield 'case_5'  => ['http://host.com/foo?bar', '/foo?bar'];
        yield 'case_6'  => ['https://host.com/foo?bar', '/foo?bar'];
        yield 'case_7'  => ['http://host.com/foo/bar', '/foo/bar'];
        yield 'case_8'  => ['https://host.com/foo/bar', '/foo/bar'];
        yield 'case_9'  => ['http://host.com/foo/bar?baz', '/foo/bar?baz'];
        yield 'case_10' => ['https://host.com/foo/bar?baz', '/foo/bar?baz'];
    }

    /**
     * @return MockObject
     */
    private function getRouterAwareTraitMock(): MockObject
    {
        return $this->getMockForTrait(
            RouterAwareTrait::class,
            [],
            '',
            true,
            true,
            true,
            [
                'locatePath',
                'removeHost',
            ]
        );
    }
}
