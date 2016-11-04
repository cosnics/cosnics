<?php
namespace Chamilo\Libraries\Hashing;

use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Libraries\Hashing
 * @author Samumon
 * @author vanpouckesven
 * @deprecated Use HashingUtilities now
 */
abstract class Hashing
{

    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     *
     * @return \Chamilo\Libraries\Hashing\Hashing
     */
    public static function get_instance()
    {
        if (! isset(self::$instance))
        {
            $type = \Chamilo\Configuration\Configuration::get('Chamilo\Configuration', 'general', 'hashing_algorithm');
            $class = __NAMESPACE__ . '\\' . StringUtilities::getInstance()->createString($type)->upperCamelize();

            if (class_exists($class))
            {
                self::$instance = new $class();
            }
        }
        return self::$instance;
    }

    /**
     *
     * @param string $value
     * @return string
     */
    public static function hash($value)
    {
        $instance = self::get_instance();
        return $instance->create_hash($value);
    }

    /**
     *
     * @param string $file
     * @return string
     */
    public static function hash_file($file)
    {
        $instance = self::get_instance();
        return $instance->create_file_hash($file);
    }

    /**
     *
     * @param string $value
     * @return string
     */
    abstract public function create_hash($value);

    /**
     *
     * @param string $file
     * @return string
     */
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
