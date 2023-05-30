<?php
/**
 * PHP version 5
 *
 * @author Systho
 */
require_once __DIR__ . '/../../../libraries/file/php/lib/path.class.php';
require_once __DIR__ . '/../../../libraries/utilities/php/lib/utilities.class.php';

use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\SystemPathBuilder;

class TestInitializer
{

    /**
     * Initialize the environment
     */
    public static function init()
    {
        self::initDefaultTimezone();
        self::initIncludePath();
        self::initAutoload();
        self::initPHPSettings();
        self::initServerGlobals();
        self::initGlobals();
    }

    private static function initAutoload()
    {
        require_once 'PHPUnit/Autoload.php';
        spl_autoload_register('libraries\utilities\Utilities::autoload');
    }

    /**
     * Initialize the default timezone which is mandatory for PHP 5
     */
    private static function initDefaultTimezone()
    {
        date_default_timezone_set('UTC');
    }

    public static function initGlobals()
    {
        $GLOBALS['language_interface'] = 'en';
    }

    /**
     * Initialize the PHP include_path
     */
    private static function initIncludePath()
    {
        $systemPathBuilder =
            DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SystemPathBuilder::class);

        $pearPath = realpath($systemPathBuilder->getPluginPath() . 'pear');
        $googleLibraryPath = realpath($systemPathBuilder->getPluginPath() . 'google/library');
        $scriptLibrariesPath = realpath($systemPathBuilder->getBasePath() . 'script/lib');

        $path = [$pearPath, $googleLibraryPath, $scriptLibrariesPath];
        $new_include_path = implode(PATH_SEPARATOR, $path) . PATH_SEPARATOR . get_include_path();

        set_include_path($new_include_path);
    }

    private static function initPHPSettings()
    {
        ini_set('error_reporting', E_ALL | E_STRICT);
        ini_set('output_buffering', 'Off');
    }

    private static function initServerGlobals()
    {
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['HTTPS'] = false;
        $_SESSION = [];
    }
}

TestInitializer::init();
