<?php
declare(strict_types=1);

/**
 * GpsLab component.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 * @license http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Sitemap\Tests\Writer;

use GpsLab\Component\Sitemap\Writer\State\Exception\WriterStateException;
use GpsLab\Component\Sitemap\Writer\TempFileWriter;
use PHPUnit\Framework\TestCase;

class TempFileWriterTest extends TestCase
{
    /**
     * @var TempFileWriter
     */
    private $writer;

    /**
     * @var string
     */
    private $filename;

    protected function setUp(): void
    {
        $this->writer = new TempFileWriter();
        $this->filename = tempnam(sys_get_temp_dir(), 'sitemap');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->filename)) {
            unlink($this->filename);
        }
    }

    public function testAlreadyStarted(): void
    {
        $this->writer->start($this->filename);

        $this->expectException(WriterStateException::class);
        $this->writer->start($this->filename);
    }

    public function testFinishNotStarted(): void
    {
        $this->expectException(WriterStateException::class);
        $this->writer->finish();
    }

    public function testAlreadyFinished(): void
    {
        $this->writer->start($this->filename);
        $this->writer->finish();

        $this->expectException(WriterStateException::class);
        $this->writer->finish();
    }

    public function testAppendNotStarted(): void
    {
        $this->expectException(WriterStateException::class);
        $this->writer->append('foo');
    }

    public function testAppendAfterFinish(): void
    {
        $this->writer->start($this->filename);
        $this->writer->finish();

        $this->expectException(WriterStateException::class);
        $this->writer->append('foo');
    }

    public function testWrite(): void
    {
        $this->writer->start($this->filename);
        $this->writer->append('foo');
        $this->writer->append('bar');
        $this->writer->finish();

        self::assertEquals('foobar', file_get_contents($this->filename));
    }
}