<?php
namespace Chamilo\Libraries\Protocol;

/**
 * Utility class to manipulate HTTP headers.
 * 
 * @author Laurent Opprecht
 */
class HttpHeader
{
    const CONTENT_TYPE_CSS = 'text/css';
    const CONTENT_TYPE_JAVASCRIPT = 'text/javascript';
    const CACHE_PUBLIC = 'public';
    const CACHE_NO_CACHE = 'no-cache';
    const HEADER_IF_MODIFIED_SINCE = 'HTTP_IF_MODIFIED_SINCE';
    const HEADER_PRAGMA = 'pragma';

    public static function content_type($type, $charset = '')
    {
        $charset = $charset ? 'charset=' . $charset : '';
        header('Content-Type: ' . $type . '; ' . $charset);
    }

    public static function expires($time)
    {
        header('Expires: ' . gmdate('D, d M Y H:i:s', $time) . ' GMT');
    }

    public static function last_modified($time)
    {
        $time = (int) min($time, PHP_INT_MAX);
        $s = 'Last-Modified: ' . gmdate('D, d M Y H:i:s', $time) . ' GMT';
        header($s);
    }

    public static function etag($tag)
    {
        header('ETag: "' . $tag . '"');
    }

    public static function cache_control($control, $ttl = null)
    {
        if (! is_null($ttl))
        {
            $control .= ', ' . 'max-age=' . (int) $ttl;
        }
        
        header('Cache-Control: ' . $control);
    }

    public static function not_modified()
    {
        header("HTTP/1.1 304 Not Modified");
    }

    public static function pragma($value)
    {
        header('Pragma: ' . $value);
    }

    public static function remove($name)
    {
        header_remove($name);
    }
}
