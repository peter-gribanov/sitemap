<?php
/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Sitemap\Render;

use GpsLab\Component\Sitemap\Url\Url;

class PlainTextSitemapRender implements SitemapRender
{
    /**
     * @return string
     */
    public function start()
    {
        return '<?xml version="1.0" encoding="utf-8"?>'.PHP_EOL.
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    }

    /**
     * @return string
     */
    public function end()
    {
        return '</urlset>'.PHP_EOL;
    }

    /**
     * @param Url $url
     *
     * @return string
     */
    public function url(Url $url)
    {
        return '<url>'.
            '<loc>'.htmlspecialchars($url->getLoc()).'</loc>'.
            '<lastmod>'.$url->getLastMod()->format('c').'</lastmod>'.
            '<changefreq>'.$url->getChangeFreq().'</changefreq>'.
            '<priority>'.$url->getPriority().'</priority>'.
        '</url>';
    }
}
