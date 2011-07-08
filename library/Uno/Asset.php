<?php
/**
 * MIT License
 * @see: http://www.opensource.org/licenses/mit-license.php
 *
 * Copyright (c) <2011> <Andrea Belvedere> <scieck@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
namespace Uno;

class Asset
{
    protected $base;

    public function __construct($basepath = NULL)
    {
        $this->base = empty($basepath) ? NULL : rtrim($basepath, '\\/') .'/';
    }

    public function setBasepath($basepath)
    {
        $this->base = rtrim($basepath, '\\/') .'/';
    }

    public function render($filename)
    {
        $filepath = empty($this->base) ? $filename : $this->base . $filename;
        if (! is_file($filepath))
        {
            header('HTTP/1.1 404 Not Found');
            if ('HEAD' != $_SERVER['REQUEST_METHOD'])
            {
                exit('<h1>404 Not Found</h1>');
            }
            exit();
        }
        $mtime = filemtime($filepath);
        $hash = '"'.md5(strval($mtime)).'"';
        $etag = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? $_SERVER['HTTP_IF_NONE_MATCH'] : NULL;

        if (! empty($etag) && ($hash == $etag))
        {
            header('HTTP/1.1 304 Not Modified');
            header('Expires: '.gmdate('D, d M Y H:i:s ', strtotime('2 days')).'GMT');
            header('Cache-Control: private, max-age=10800, pre-check=10800');
            header('Pragma: private');
            header('Etag: '.$hash);
            exit();
        }
        header('Expires: '.gmdate('D, d M Y H:i:s ', strtotime('2 days')).'GMT');
        header('Cache-Control: private, max-age=10800, pre-check=10800');
        header('Pragma: private');
        // i.e. Mon, 24 Jan 2011 9:45:40 GMT
        header('Last-Modified: '.gmdate('D, d M Y H:i:s ', $mtime).'GMT');
        header('Etag: '.$hash);
        header('Content-Length: '.filesize($filepath));
        header('Content-Type: '.\Uno\Mime::type($filename));
        readfile($filepath);
        exit();
    }
}