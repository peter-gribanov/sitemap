# Upgrade from 1.1 to 2.0

* The `SilentSitemapBuilder` was removed.
* The `SymfonySitemapBuilder` was removed.
* The `CompressFileStream` was removed.
* The `RenderBzip2FileStream` was removed.
* The `Stream` not extends `Countable` interface.
* The `UrlBuilder` not extends `Countable` interface and not require `getName` method.
* The `UrlBuilderCollection` changed to `MultiUrlBuilder`.
* The `CompressionLevelException` changed to final.
* The `FileAccessException` changed to final.
* The `LinksOverflowException` changed to final.
* The `OverflowException` changed to abstract.
* The `SizeOverflowException` changed to final.
* The `StreamStateException` changed to final.
* The `$compression_level` in `RenderGzipFileStream` can be only integer.
* Move `CHANGE_FREQ_*` constants from `URL` class to new `ChangeFreq` class.
* Mark `STATE_*` constants in `StreamState` class as private.
* The `Url::getLoc()` was renamed to `Url::getLocation()` method.
* The `Url::getLastMod()` was renamed to `Url::getLastModify()` method.
* The arguments of `PlainTextSitemapRender::sitemap()` was changed.

  Before:

  ```php
  PlainTextSitemapRender::sitemap(string $path, ?\DateTimeInterface $last_modify = null)
  ```

  After:

  ```php
  PlainTextSitemapRender::sitemap(Sitemap $sitemap)
  ```

* The `$host` argument in `RenderIndexFileStream::__construct()` was removed.
* The `$web_path` argument in `PlainTextSitemapIndexRender::__construct()` was added.

  Before:

  ```php
  $web_path = 'https://example.com/';
  $index_render = new PlainTextSitemapIndexRender();
  $index_stream = new RenderFileStream($index_render, $stream, $web_path, $filename_index);
  ```

  After:

  ```php
  $web_path = 'https://example.com'; // No slash in end of path!
  $index_render = new PlainTextSitemapIndexRender($web_path);
  $index_stream = new RenderFileStream($index_render, $stream, $filename_index);
  ```

* The `$web_path` argument in `PlainTextSitemapRender::__construct()` was added.

  Before:

  ```php
  $render = new PlainTextSitemapRender();
  $render->url(new Url('https://example.com'));
  $render->url(new Url('https://example.com/about'));
  ```

  After:

  ```php
  $web_path = 'https://example.com'; // No slash in end of path!
  $render = new PlainTextSitemapRender($web_path);
  $render->url(new Url(''));
  $render->url(new Url('/about'));
  ```