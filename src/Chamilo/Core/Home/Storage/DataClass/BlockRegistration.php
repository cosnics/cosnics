<?php
namespace Chamilo\Core\Home\Storage\DataClass;

use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package home.lib
 */
class BlockRegistration extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const PROPERTY_CONTEXT = 'context';
    const PROPERTY_BLOCK = 'block';

    /**
     * Get the default properties of all user course categories.
     * 
     * @return multitype:string The property names.
     */
    public static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_CONTEXT, self :: PROPERTY_BLOCK));
    }
    
    /*
     * (non-PHPdoc) @see common\libraries.DataClass::get_data_manager()
     */
    public function get_data_manager()
    {
        return DataManager :: get_instance();
    }

    /**
     *
     * @return string
     */
    public function get_context()
    {
        return $this->get_default_property(self :: PROPERTY_CONTEXT);
    }

    public function set_context($context)
    {
        $this->set_default_property(self :: PROPERTY_CONTEXT, $context);
    }

    /**
     *
     * @return string
     */
    public function get_block()
    {
        return $this->get_default_property(self :: PROPERTY_BLOCK);
    }

    public function set_block($block)
    {
        $this->set_default_property(self :: PROPERTY_BLOCK, $block);
    }

    public function delete()
    {
        if (! parent :: delete())
        {
            return false;
        }
        else
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(Block :: class_name(), Block :: PROPERTY_REGISTRATION_ID), 
                new StaticConditionVariable($this->get_id()));
            $blocks = DataManager :: retrieves(Block :: class_name(), $condition);
            
            while ($block = $blocks->next_result())
            {
                if (! $block->delete())
                {
                    return false;
                }
            }
        }
        
        return true;
    }
}
