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

use Behat\Symfony2Extension\Context\KernelDictionary;
use Symfony\Component\Routing\Exception\ExceptionInterface;

/**
 * @author Rémi Marseille <remi.marseille@ekino.com>
 */
trait RouterAwareTrait
{
    /**
     * {@inheritdoc}
     *
     * Calls the Symfony router first, fallback to the initial behavior.
     * So given $path can be a route, even with parameters: my_route;id=12&foo=bar
     */
    public function locatePath($path)
    {
        if (!\in_array(KernelDictionary::class, class_uses($this))) {
            throw new \RuntimeException(sprintf('Please use the trait %s in the class %s', KernelDictionary::class, __CLASS__));
        }

        try {
            $parts      = explode(';', $path);
            $parameters = [];

            if (isset($parts[1])) {
                parse_str($parts[1], $parameters);
            }

            $url = $this->getContainer()->get('router')->generate($parts[0], $parameters);
        } catch (ExceptionInterface $e) {
            $url = $path;
        }

        $locatedPath = parent::locatePath($url);

        // add logs, captured by Behat
        echo $locatedPath;

        return $locatedPath;
    }
}
