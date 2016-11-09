<?php
namespace Chamilo\Configuration\Package\Properties\Dependencies\Dependency;

use Chamilo\Libraries\Format\MessageLogger;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 *
 * @package Chamilo\Configuration\Package\Properties\Dependencies\Dependency
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Dependency
{
    const PROPERTY_ID = 'id';
    const TYPE_PACKAGE = 'package';
    const TYPE_EXTENSIONS = 'extensions';
    const TYPE_SERVER = 'server';
    const TYPE_SETTINGS = 'settings';

    private $id;

    protected $logger;

    public function __construct()
    {
        $this->logger = MessageLogger :: getInstance($this);
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
     * @param string $type
     * @throws Exception
     * @return Dependency
     */
    public static function factory($type)
    {
        $class = __NAMESPACE__ . '\\' . StringUtilities :: getInstance()->createString($type)->upperCamelize() .
             'Dependency';

        if (! class_exists($class))
        {
            throw new Exception(Translation :: get('TypeDoesNotExist', array('type' => $type)));
        }

        return new $class();
    }

    public function get_logger()
    {
        return $this->logger;
    }

    abstract public function check();

    abstract public function as_html();

    public function compare($type, $reference, $value)
    {
        switch ($type)
        {
            case self :: COMPARE_EQUAL :
                return ($reference == $value);
                break;
            case self :: COMPARE_NOT_EQUAL :
                return ($reference != $value);
                break;
            case self :: COMPARE_GREATER_THEN :
                return ($value > $reference);
                break;
            case self :: COMPARE_GREATER_THEN_OR_EQUAL :
                return ($value >= $reference);
                break;
            case self :: COMPARE_LESS_THEN :
                return ($value < $reference);
                break;
            case self :: COMPARE_LESS_THEN_OR_EQUAL :
                return ($value <= $reference);
                break;
            default :
                return false;
                break;
        }
    }

    public static function from_dom_node($dom_xpath, $dom_node)
    {
        $class = self :: type($dom_node->getAttribute('type'));
        return $class :: dom_node($dom_xpath, $dom_node);
    }

    public static function dom_node($dom_xpath, $dom_node)
    {
        $dependency = self :: factory($dom_node->getAttribute('type'));
        $dependency->set_id(trim($dom_xpath->query('id', $dom_node)->item(0)->nodeValue));
        return $dependency;
    }

    public static function type($type)
    {
        return __NAMESPACE__ . '\\' . StringUtilities :: getInstance()->createString($type)->upperCamelize() .
             'Dependency';
    }
}
