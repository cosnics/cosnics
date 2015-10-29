<?php
namespace Chamilo\Libraries\Platform\Configuration;

use Chamilo\Libraries\File\Path;

/**
 * $Id: configuration.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 *
 * @package common.configuration
 */

/**
 * This class represents the current configuration.
 *
 * @author Tim De Pauw
 */
class Configuration
{

    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Parameters defined in the configuration.
     * Stored as an associative array.
     */
    private $params;

    /**
     * Constructor.
     */
    private function __construct()
    {
        $this->load_file(Path :: getInstance()->getStoragePath() . 'configuration/configuration.ini');
    }

    /**
     * Returns the instance of this class.
     *
     * @return Configuration The instance.
     */
    public static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new self();
        }
        return self :: $instance;
    }

    /**
     * Gets a parameter from the configuration.
     *
     * @param string $section The name of the section in which the parameter is located.
     * @param string $name The parameter name.
     * @return mixed The parameter value.
     */
    public function get_parameter($section, $name)
    {
        if (! isset($this->params[$section]))
            return null;
        if (! isset($this->params[$section][$name]))
            return null;

        return $this->params[$section][$name];
    }

    /**
     * Load the config from a given file.
     *
     * @param string $file the php file which must be loaded.
     */
    public function load_file($file)
    {
        if (! is_file($file))
        {
            throw new \Exception("Config file {$file} not found");
        }

        if (! is_readable($file))
        {
            throw new \Exception("Config file {$file} not readable");
        }

        $this->params = parse_ini_file($file, true);
    }
}
