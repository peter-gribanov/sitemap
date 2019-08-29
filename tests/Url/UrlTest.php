<?php
declare(strict_types=1);

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011-2019, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Sitemap\Tests\Url;

use GpsLab\Component\Sitemap\Url\ChangeFreq;
use GpsLab\Component\Sitemap\Url\Exception\InvalidLastModifyException;
use GpsLab\Component\Sitemap\Url\Exception\InvalidPriorityException;
use GpsLab\Component\Sitemap\Url\Url;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    public function testDefaultUrl(): void
    {
        $location = '';
        $url = new Url($location);

        self::assertEquals($location, $url->getLocation());
        self::assertNull($url->getLastModify());
        self::assertNull($url->getChangeFreq());
        self::assertNull($url->getPriority());
    }

    /**
     * @return array
     */
    public function getUrls(): array
    {
        return [
            [new \DateTimeImmutable('-10 minutes'), ChangeFreq::ALWAYS, '1.0'],
            [new \DateTimeImmutable('-1 hour'), ChangeFreq::HOURLY, '1.0'],
            [new \DateTimeImmutable('-1 day'), ChangeFreq::DAILY, '0.9'],
            [new \DateTimeImmutable('-1 week'), ChangeFreq::WEEKLY, '0.5'],
            [new \DateTimeImmutable('-1 month'), ChangeFreq::MONTHLY, '0.2'],
            [new \DateTimeImmutable('-1 year'), ChangeFreq::YEARLY, '0.1'],
            [new \DateTimeImmutable('-2 year'), ChangeFreq::NEVER, '0.0'],
            [new \DateTime('-10 minutes'), ChangeFreq::ALWAYS, '1.0'],
            [new \DateTime('-1 hour'), ChangeFreq::HOURLY, '1.0'],
            [new \DateTime('-1 day'), ChangeFreq::DAILY, '0.9'],
            [new \DateTime('-1 week'), ChangeFreq::WEEKLY, '0.5'],
            [new \DateTime('-1 month'), ChangeFreq::MONTHLY, '0.2'],
            [new \DateTime('-1 year'), ChangeFreq::YEARLY, '0.1'],
            [new \DateTime('-2 year'), ChangeFreq::NEVER, '0.0'],
        ];
    }

    /**
     * @dataProvider getUrls
     *
     * @param \DateTimeInterface $last_modify
     * @param string             $change_freq
     * @param string             $priority
     */
    public function testCustomUrl(\DateTimeInterface $last_modify, string $change_freq, string $priority): void
    {
        $location = '/index.html';

        $url = new Url($location, $last_modify, $change_freq, $priority);

        self::assertEquals($location, $url->getLocation());
        self::assertEquals($last_modify, $url->getLastModify());
        self::assertEquals($change_freq, $url->getChangeFreq());
        self::assertEquals($priority, $url->getPriority());
    }

    public function testInvalidLastModify(): void
    {
        $this->expectException(InvalidLastModifyException::class);

        new Url('/', new \DateTimeImmutable('+1 minutes'));
    }

    public function testInvalidPriority(): void
    {
        $this->expectException(InvalidPriorityException::class);

        new Url('/', null, null, '');
    }
}
