<?php
namespace Chamilo\Libraries\Platform\Configuration;

use Chamilo\Libraries\File\Path;
use Exception;

/**
 * This class represents the current configuration.
 *
 * @package Chamilo\Libraries\Platform\Configuration
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
     *
     * @var string[]
     */
    private $params;

    /**
     * Constructor.
     */
    private function __construct()
    {
        $this->load_file(Path::getInstance()->getStoragePath() . 'configuration/configuration.ini');
    }

    /**
     * Returns the instance of this class.
     *
     * @return \Chamilo\Libraries\Platform\Configuration\Configuration The instance.
     */
    public static function getInstance()
    {
        if (! isset(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Gets a parameter from the configuration.
     *
     * @param string $section The name of the section in which the parameter is located.
     * @param string $name The parameter name.
     *
     * @return string The parameter value.
     *
     * @deprecated
     *
     * @see getParameter
     */
    public function get_parameter($section, $name)
    {
        return $this->getParameter($section, $name);
    }

    /**
     * Gets a parameter from the configuration.
     *
     * @param string $section The name of the section in which the parameter is located.
     * @param string $name The parameter name.
     *
     * @return string The parameter value.
     */
    public function getParameter($section, $name)
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
     *
     * @throws \Exception
     *
     * @deprecated
     *
     * @see loadFile
     */
    public function load_file($file)
    {
        $this->load_file($file);
    }

    /**
     * Load the config from a given file.
     *
     * @param string $file the php file which must be loaded.
     *
     * @throws \Exception
     */
    public function loadFile($file)
    {
        if (! is_file($file))
        {
            throw new Exception("Config file {$file} not found");
        }

        if (! is_readable($file))
        {
            throw new Exception("Config file {$file} not readable");
        }

        $this->params = parse_ini_file($file, true);
    }
}
