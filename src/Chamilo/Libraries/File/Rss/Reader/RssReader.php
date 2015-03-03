<?php
namespace Chamilo\Libraries\File\Rss\Reader;

use Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Class that reads and parses rss feed
 *
 * @package common\libraries
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class RssReader
{
    const TYPE_RSS_PHP = 'rss_php';

    /**
     * Keeps instances of the rss readers per type
     *
     * @var RssReader[string]
     */
    private static $instances;

    /**
     * Factory method to launch the correct RSS reader
     *
     * @param string $type
     *
     * @throws ClassNotExistException
     *
     * @return RssReader
     */
    public static function factory($type = self :: TYPE_RSS_PHP)
    {
        if (! self :: $instances[$type])
        {
            $class = __NAMESPACE__ . '\Implementation\\' . StringUtilities :: getInstance()->createString($type)->upperCamelize() . 'RssReader';
            if (! class_exists($class, true))
            {
                throw new ClassNotExistException($type);
            }

            self :: $instances[$type] = new $class();
        }

        return self :: $instances[$type];
    }

    /**
     * Parses a url and returns the rss items
     *
     * @param string $url
     * @param int $number_of_items
     *
     * @return string[]
     */
    abstract public function parse_url($url, $number_of_items = 5);
}