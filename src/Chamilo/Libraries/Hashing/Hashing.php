<?php
namespace Chamilo\Libraries\Hashing;

use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * $Id: hashing.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 *
 * @package common.hashing
 */
/**
 * Class that defines a hashing framework so people choose which hashing algorithm to use
 *
 * @author vanpouckesven
 */
abstract class Hashing
{

    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    public static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = \Chamilo\Configuration\Configuration :: get('Chamilo\Configuration', 'general', 'hashing_algorithm');
            $class = __NAMESPACE__ . '\\' . StringUtilities :: getInstance()->createString($type)->upperCamelize();

            if (class_exists($class))
            {
                self :: $instance = new $class();
            }
        }
        return self :: $instance;
    }

    public static function hash($value)
    {
        $instance = self :: get_instance();
        return $instance->create_hash($value);
    }

    public static function hash_file($file)
    {
        $instance = self :: get_instance();
        return $instance->create_file_hash($file);
    }

    abstract public function create_hash($value);

    abstract public function create_file_hash($file);

    /**
     *
     * @return string[]
     */
    public static function get_available_types()
    {
        return array(
            'Haval256' => 'HAVAL-256',
            'Md5' => 'MD5',
            'Sha1' => 'SHA-1',
            'Sha512' => 'SHA-512',
            'Whirlpool' => 'Whirlpool');
    }
}
