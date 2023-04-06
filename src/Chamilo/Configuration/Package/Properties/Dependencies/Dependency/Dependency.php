<?php
namespace Chamilo\Configuration\Package\Properties\Dependencies\Dependency;

use Chamilo\Libraries\Format\MessageLogger;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Configuration\Package\Properties\Dependencies\Dependency
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Dependency
{
    const PROPERTY_ID = 'id';
    const PROPERTY_VERSION = 'version';
    const TYPE_PACKAGE = 'package';
    const TYPE_EXTENSIONS = 'extensions';
    const TYPE_SERVER = 'server';
    const TYPE_SETTINGS = 'settings';
    const COMPARE_EQUAL = '=';
    const COMPARE_NOT_EQUAL = '!=';
    const COMPARE_GREATER_THEN = '>';
    const COMPARE_GREATER_THEN_OR_EQUAL = '>=';
    const COMPARE_LESS_THEN = '<';
    const COMPARE_LESS_THEN_OR_EQUAL = '<=';

    private $id;

    /**
     *
     * @var string
     */
    private $version;

    protected $logger;

    public function __construct()
    {
        $this->logger = MessageLogger::getInstance($this);
    }

    public function get_id()
    {
        return $this->id;
    }

    /**
     *
     * @param $id the $id to set
     */
    public function set_id($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return string
     */
    public function get_version()
    {
        return $this->version;
    }

    /**
     *
     * @param string $version
     */
    public function set_version($version)
    {
        $this->version = $version;
    }

    /**
     *
     * @param string $type
     * @throws Exception
     * @return Dependency
     */
    public static function factory($type)
    {
        $class = __NAMESPACE__ . '\\' . StringUtilities::getInstance()->createString($type)->upperCamelize() .
             'Dependency';

        if (! class_exists($class))
        {
            throw new Exception(Translation::get('TypeDoesNotExist', array('type' => $type)));
        }

        return new $class();
    }

    public function get_logger()
    {
        return $this->logger;
    }

    /**
     * Checks the dependency in the registration table of the administration
     *
     * @return boolean
     */
    public function check()
    {
        $parameters = array();
        $parameters['REQUIREMENT'] = $this->as_html();

        $message = Translation::get('DependencyCheckRegistration') . ': ' . $this->as_html() . ' ' . Translation::get(
            'Found',
            array(),
            Utilities::COMMON_LIBRARIES) . ': ';
        $registration = \Chamilo\Configuration\Configuration::registration($this->get_id());

        if (empty($registration))
        {
            $parameters['CURRENT'] = '--' . Translation::get('Nothing', array(), Utilities::COMMON_LIBRARIES) . '--';
            $this->logger->add_message(Translation::get('CurrentDependency', $parameters), MessageLogger::TYPE_ERROR);
            return false;
        }
        else
        {
            $target_version = \Composer\Semver\Semver::satisfies(
                $registration[Registration::PROPERTY_VERSION],
                $this->get_version());

            if (! $target_version)
            {
                $parameters['CURRENT'] = '--' . Translation::get('WrongVersion', array(), Utilities::COMMON_LIBRARIES) . '--';
                $this->logger->add_message(
                    Translation::get('CurrentDependency', $parameters),
                    MessageLogger::TYPE_ERROR);
                return false;
            }
            else
            {
                if (! $registration[Registration::PROPERTY_STATUS])
                {
                    $parameters['CURRENT'] = '--' .
                         Translation::get('InactiveObject', array(), Utilities::COMMON_LIBRARIES) . '--';
                    $this->logger->add_message(
                        Translation::get('CurrentDependency', $parameters),
                        MessageLogger::TYPE_ERROR);
                    return false;
                }
                else
                {
                    $this->logger->add_message($parameters['REQUIREMENT']);
                    return true;
                }
            }
        }
    }

    /**
     * Creates a dependency information string as html
     *
     * @return String
     */
    public function as_html()
    {
        $parameters = array();
        $parameters['ID'] = $this->get_id();
        $parameters['VERSION'] = $this->get_version();

        return Translation::get('Dependency', $parameters);
    }

    public function compare($type, $reference, $value)
    {
        switch ($type)
        {
            case self::COMPARE_EQUAL :
                return ($reference == $value);
                break;
            case self::COMPARE_NOT_EQUAL :
                return ($reference != $value);
                break;
            case self::COMPARE_GREATER_THEN :
                return ($value > $reference);
                break;
            case self::COMPARE_GREATER_THEN_OR_EQUAL :
                return ($value >= $reference);
                break;
            case self::COMPARE_LESS_THEN :
                return ($value < $reference);
                break;
            case self::COMPARE_LESS_THEN_OR_EQUAL :
                return ($value <= $reference);
                break;
            default :
                return false;
                break;
        }
    }

    public static function from_dom_node($dom_xpath, $dom_node)
    {
        $class = self::type($dom_node->getAttribute('type'));
        return $class::dom_node($dom_xpath, $dom_node);
    }

    public static function dom_node($dom_xpath, $dom_node)
    {
        $dependency = self::factory($dom_node->getAttribute('type'));
        $dependency->set_id(trim($dom_xpath->query('id', $dom_node)->item(0)->nodeValue));
        $version_node = $dom_xpath->query('version', $dom_node)->item(0);
        $version = new Version($version_node->nodeValue, $version_node->getAttribute('operator'));

        $dependency->set_version($version);
        return $dependency;
    }

    public static function type($type)
    {
        return __NAMESPACE__ . '\\' . StringUtilities::getInstance()->createString($type)->upperCamelize() . 'Dependency';
    }

    /**
     *
     * @param string $context
     * @return boolean
     */
    public function needs($context)
    {
        return $this->get_id() == $context;
    }
}
