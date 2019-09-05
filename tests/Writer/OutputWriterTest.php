<?php
declare(strict_types=1);

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011-2019, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Sitemap\Tests\Writer;

use GpsLab\Component\Sitemap\Writer\OutputWriter;
use PHPUnit\Framework\TestCase;

class OutputWriterTest extends TestCase
{
    /**
     * @var OutputWriter
     */
    private $writer;

    protected function setUp(): void
    {
        $this->writer = new OutputWriter();
    }

    public function testWrite(): void
    {
        ob_start();
        $this->writer->start(''); // not use filename
        $this->writer->append('foo');
        $this->writer->append('bar');
        $this->writer->finish();

        self::assertEquals('foobar', ob_get_clean());
    }
}
