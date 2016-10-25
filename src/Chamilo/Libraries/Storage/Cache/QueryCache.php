<?php
namespace Chamilo\Libraries\Storage\Cache;

use Exception;

/**
 *
 * @package Chamilo\Libraries\Storage\Cache
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Should no longer be used
 */
class QueryCache
{

    /**
     * The instance of the QueryCache
     *
     * @var \Chamilo\Libraries\Storage\Cache\QueryCache
     */
    private static $instance;

    /**
     * The cache
     *
     * @var mixed[][]
     */
    private $cache;

    /**
     * Get an instance of the QueryCache
     *
     * @return \Chamilo\Libraries\Storage\Cache\QueryCache
     */
    public static function get_instance()
    {
        if (! isset(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     *
     * @param string $hash
     * @return boolean
     */
    public static function get($hash)
    {
        $instance = self::get_instance();

        if (self::exists($hash))
        {
            return $instance->cache[$hash];
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * @param string $hash
     * @return boolean
     */
    public static function exists($hash)
    {
        $instance = self::get_instance();

        if (isset($instance->cache[$hash]))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * @param string $class
     * @return boolean
     */
    public static function truncate()
    {
        $instance = self::get_instance();

        if (isset($instance->cache))
        {
            unset($instance->cache);
        }

        return true;
    }

    /**
     *
     * @param string $hash
     * @param mixed $value
     */
    public static function set_cache($hash, $value)
    {
        $instance = self::get_instance();
        $instance->cache[$hash] = $value;
    }

    public static function reset()
    {
        $instance = self::get_instance();
        $instance->cache = array();
    }

    /**
     *
     * @param mixed $result
     * @param string $hash
     * @throws Exception
     * @return boolean
     */
    public static function add($result, $hash)
    {
        if (! is_string($hash) && strlen($hash) != 32)
        {
            throw new Exception('Illegal hash passed to the QueryCache');
        }

        if (! self::get($hash))
        {
            self::set_cache($hash, $result);
        }

        return true;
    }

    /**
     *
     * @return string
     */
    public static function hash()
    {
        $backtrace = debug_backtrace(null, 2);
        $called_class = $backtrace[1];

        $parts = array();
        $parts[] = $called_class['class'];
        $parts[] = $called_class['type'];
        $parts[] = $called_class['function'];

        foreach ($called_class['args'] as $argument)
        {
            $parts[] = $argument;
        }

        return md5(serialize($parts));
    }
}
