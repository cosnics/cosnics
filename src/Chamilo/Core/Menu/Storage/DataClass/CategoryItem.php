<?php
namespace Chamilo\Core\Menu\Storage\DataClass;

use Chamilo\Core\Menu\Storage\DataManager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Core\Menu\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CategoryItem extends Item
{

    private $children;

    public function __construct($default_properties = array(), $additional_properties = null)
    {
        parent::__construct($default_properties, $additional_properties);
        $this->set_type(__CLASS__);
    }

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name());
    }

    public function get_children()
    {
        if (! isset($this->children))
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(Item::class_name(), Item::PROPERTY_PARENT), 
                new StaticConditionVariable($this->get_id()));
            $parameters = new DataClassRetrievesParameters(
                $condition, 
                null, 
                null, 
                new OrderBy(new PropertyConditionVariable(Item::class_name(), Item::PROPERTY_SORT)));
            $items = DataManager::retrieves(Item::class_name(), $parameters);
            $this->children = $items->as_array();
        }
        return $this->children;
    }

    public function set_children($children)
    {
        $this->children = $children;
    }

    public function has_children()
    {
        return count($this->get_children()) > 0;
    }

    public function add_child($child)
    {
        $this->children[] = $child;
    }

    public function delete()
    {
        foreach ($this->get_children() as $child)
        {
            if (! $child->delete())
            {
                return false;
            }
        }
        
        return parent::delete();
    }
}
