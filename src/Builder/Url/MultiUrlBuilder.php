<?php
declare(strict_types=1);

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011-2019, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Sitemap\Builder\Url;

use GpsLab\Component\Sitemap\Url\Url;

class MultiUrlBuilder implements UrlBuilder
{
    /**
     * @var iterable[]
     */
    private $builders = [];

    /**
     * @param iterable[] $builders
     */
    public function __construct(array $builders = [])
    {
        foreach ($builders as $builder) {
            $this->add($builder);
        }
    }

    /**
     * @param iterable $builder
     */
    public function add(iterable $builder): void
    {
        $this->builders[] = $builder;
    }

    /**
     * @return Url[]|\Generator
     */
    public function getIterator(): \Traversable
    {
        foreach ($this->builders as $builder) {
            foreach ($builder as $url) {
                yield $url;
            }
        }
    }
}