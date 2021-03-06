<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Sitemap\Stream;

use GpsLab\Component\Sitemap\Render\SitemapRender;
use GpsLab\Component\Sitemap\Stream\Exception\LinksOverflowException;
use GpsLab\Component\Sitemap\Stream\Exception\SizeOverflowException;
use GpsLab\Component\Sitemap\Stream\Exception\StreamStateException;
use GpsLab\Component\Sitemap\Stream\State\StreamState;
use GpsLab\Component\Sitemap\Url\Url;

class OutputStream implements Stream
{
    /**
     * @var SitemapRender
     */
    private $render;

    /**
     * @var StreamState
     */
    private $state;

    /**
     * @var int
     */
    private $counter = 0;

    /**
     * @var int
     */
    private $used_bytes = 0;

    /**
     * @var string
     */
    private $end_string = '';

    /**
     * @param SitemapRender $render
     */
    public function __construct(SitemapRender $render)
    {
        $this->render = $render;
        $this->state = new StreamState();
    }

    public function open()
    {
        $this->state->open();
        $this->send($this->render->start());
        // render end string only once
        $this->end_string = $this->render->end();
    }

    public function close()
    {
        $this->state->close();
        $this->send($this->end_string);
        $this->counter = 0;
        $this->used_bytes = 0;
    }

    /**
     * @param Url $url
     */
    public function push(Url $url)
    {
        if (!$this->state->isReady()) {
            throw StreamStateException::notReady();
        }

        if ($this->counter >= self::LINKS_LIMIT) {
            throw LinksOverflowException::withLimit(self::LINKS_LIMIT);
        }

        $render_url = $this->render->url($url);
        $expected_bytes = $this->used_bytes + strlen($render_url) + strlen($this->end_string);

        if ($expected_bytes > self::BYTE_LIMIT) {
            throw SizeOverflowException::withLimit(self::BYTE_LIMIT);
        }

        $this->send($render_url);
        ++$this->counter;
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->counter;
    }

    /**
     * @param string $string
     */
    private function send($string)
    {
        echo $string;
        flush();
        $this->used_bytes += strlen($string);
    }
}
