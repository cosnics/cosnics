<?php
namespace Chamilo\Core\Repository\Selector;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Render content object type selection tabs based on their category
 * 
 * @author Hans De Bisschop
 */
abstract class TypeSelectorRenderer
{
    CONST TYPE_FULL = 'full';
    CONST TYPE_FORM = 'form';
    CONST TYPE_TABS = 'tabs';

    /**
     *
     * @var \libraries\architecture\application\Application
     */
    private $parent;

    /**
     *
     * @var \core\repository\TypeSelector
     */
    private $type_selector;

    /**
     *
     * @param Application $parent
     * @param TypeSelector $type_selector
     */
    public function __construct(Application $parent, TypeSelector $type_selector)
    {
        $this->parent = $parent;
        $this->type_selector = $type_selector;
    }

    /**
     *
     * @return \libraries\architecture\application\Application
     */
    public function get_parent()
    {
        return $this->parent;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Selector\TypeSelector
     */
    public function get_type_selector()
    {
        return $this->type_selector;
    }

    abstract function render();

    public static function factory($type, Application $parent, TypeSelector $type_selector)
    {
        $class_name = __NAMESPACE__ . '\\' . StringUtilities::getInstance()->createString($type)->upperCamelize() .
             'TypeSelectorRenderer';
        $arguments = func_get_args();
        
        switch ($type)
        {
            case self::TYPE_FORM :
                return new $class_name($parent, $type_selector, $arguments[3]);
                break;
            case self::TYPE_TABS :
                return new $class_name($parent, $type_selector, $arguments[3], $arguments[4]);
                break;
            case self::TYPE_FULL :
                return new $class_name($parent, $type_selector, $arguments[3], $arguments[4], $arguments[5]);
                break;
        }
    }
}
