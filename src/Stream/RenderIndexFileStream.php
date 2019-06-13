<?php
declare(strict_types=1);

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011-2019, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Sitemap\Stream;

use GpsLab\Component\Sitemap\Render\SitemapIndexRender;
use GpsLab\Component\Sitemap\Stream\Exception\FileAccessException;
use GpsLab\Component\Sitemap\Stream\Exception\IndexStreamException;
use GpsLab\Component\Sitemap\Stream\Exception\OverflowException;
use GpsLab\Component\Sitemap\Stream\Exception\StreamStateException;
use GpsLab\Component\Sitemap\Stream\State\StreamState;
use GpsLab\Component\Sitemap\Url\Url;

class RenderIndexFileStream implements FileStream
{
    /**
     * @var SitemapIndexRender
     */
    private $render;

    /**
     * @var FileStream
     */
    private $substream;

    /**
     * @var StreamState
     */
    private $state;

    /**
     * @var resource|null
     */
    private $handle;

    /**
     * @var string
     */
    private $filename = '';

    /**
     * @var string
     */
    private $tmp_filename = '';

    /**
     * @var int
     */
    private $index = 0;

    /**
     * @var bool
     */
    private $empty_index = true;

    /**
     * @param SitemapIndexRender $render
     * @param FileStream         $substream
     * @param string             $filename
     */
    public function __construct(SitemapIndexRender $render, FileStream $substream, string $filename)
    {
        $this->render = $render;
        $this->substream = $substream;
        $this->filename = $filename;
        $this->state = new StreamState();
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    public function open(): void
    {
        $this->state->open();
        $this->substream->open();
        $this->tmp_filename = tempnam(sys_get_temp_dir(), 'sitemap_index');

        if (($this->handle = @fopen($this->tmp_filename, 'wb')) === false) {
            throw FileAccessException::notWritable($this->tmp_filename);
        }
        fwrite($this->handle, $this->render->start());
    }

    public function close(): void
    {
        $this->state->close();
        $this->substream->close();

        if (!$this->empty_index) {
            $this->addSubStreamFileToIndex();
        }

        fwrite($this->handle, $this->render->end());
        fclose($this->handle);

        if (!rename($this->tmp_filename, $this->filename)) {
            unlink($this->tmp_filename);
            throw FileAccessException::failedOverwrite($this->tmp_filename, $this->filename);
        }

        $this->handle = null;
        $this->tmp_filename = '';
    }

    /**
     * @param Url $url
     */
    public function push(Url $url): void
    {
        if (!$this->state->isReady()) {
            throw StreamStateException::notReady();
        }

        try {
            $this->substream->push($url);
        } catch (OverflowException $e) {
            $this->substream->close();
            $this->addSubStreamFileToIndex();
            $this->substream->open();
            $this->substream->push($url);
        }

        $this->empty_index = false;
    }

    private function addSubStreamFileToIndex(): void
    {
        $filename = $this->substream->getFilename();
        $indexed_filename = $this->getIndexPartFilename($filename, ++$this->index);

        if (!is_file($filename) || !($time = filemtime($filename))) {
            throw IndexStreamException::undefinedSubstreamFile($filename);
        }

        $last_mod = (new \DateTimeImmutable())->setTimestamp($time);

        // rename sitemap file to the index part file
        $new_filename = dirname($filename).'/'.$indexed_filename;
        if (!rename($filename, $new_filename)) {
            throw IndexStreamException::failedRename($filename, $new_filename);
        }

        fwrite($this->handle, $this->render->sitemap($indexed_filename, $last_mod));
    }

    /**
     * @param string $path
     * @param int    $index
     *
     * @return string
     */
    private function getIndexPartFilename(string $path, int $index): string
    {
        // use explode() for correct add index
        // sitemap.xml -> sitemap1.xml
        // sitemap.xml.gz -> sitemap1.xml.gz

        list($filename, $extension) = explode('.', basename($path), 2) + ['', ''];

        return sprintf('%s%s.%s', $filename, $index, $extension);
    }
}
