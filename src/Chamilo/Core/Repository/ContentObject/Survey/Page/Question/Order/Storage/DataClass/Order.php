<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Order\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Order\Storage\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package repository\content_object\survey_order_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class Order extends ContentObject implements Versionable
{
    const CLASS_NAME = __CLASS__;
    
    // Properties
    const PROPERTY_DISPLAY_TYPE = 'display_type';
    const PROPERTY_ORDER_LIMIT = 'order_limit';
    const PROPERTY_QUESTION = 'question';
    const PROPERTY_INSTRUCTION = 'instruction';
    // Pseudo-property, constant can be used when handling the collection of options
    const PROPERTY_OPTIONS = 'options';
    
    // Display types
    const DISPLAY_TYPE_ONE_COLUMN = 'one_column';
    const DISPLAY_TYPE_TWO_COLUMN = 'two_column';

    private $options = array();

    static function get_type_name()
    {
        return ClassnameUtilities :: getInstance()->getClassNameFromNamespace(self :: CLASS_NAME, true);
    }

    static function get_additional_property_names()
    {
        return array(
            self :: PROPERTY_DISPLAY_TYPE, 
            self :: PROPERTY_ORDER_LIMIT, 
            self :: PROPERTY_QUESTION, 
            self :: PROPERTY_INSTRUCTION);
    }

    public function create()
    {
        $success = parent :: create();
        
        if ($success)
        {
            foreach ($this->options as $option)
            {
                $option->set_question_id($this->get_id());
                $option->create();
            }
        }
        return $success;
    }

    public function get_options()
    {
        if (! $this->get_id())
        {
            return $this->options;
        }
        else
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    OrderOption :: class_name(), 
                    OrderOption :: PROPERTY_QUESTION_ID), 
                new StaticConditionVariable($this->get_id()));
            
            $order = new OrderBy(
                new PropertyConditionVariable(
                    OrderOption :: class_name(), 
                    OrderOption :: PROPERTY_DISPLAY_ORDER));
            
            $this->options = DataManager :: retrieves(
                OrderOption :: class_name(), 
                new DataClassRetrievesParameters($condition, null, null, array($order)))->as_array();
            return $this->options;
        }
    }

    public function get_number_of_options()
    {
        return count($this->get_options());
        
        // if (! $this->get_id())
        // {
        // return count($this->options);
        // }
        // else
        // {
        // $condition = new EqualityCondition(
        // new PropertyConditionVariable(
        // OrderOption :: class_name(),
        // OrderOption :: PROPERTY_QUESTION_ID),
        // new StaticConditionVariable($this->get_id()));
        
        // return DataManager :: count(
        // OrderOption :: class_name(),
        // new DataClassCountParameters($condition));
        // }
    }

    public function add_option(OrderOption $option)
    {
        $this->options[$option->get_display_order()] = $option;
    }

    public function set_display_type($type)
    {
        return $this->set_additional_property(self :: PROPERTY_DISPLAY_TYPE, $type);
    }

    public function get_display_type()
    {
        return $this->get_additional_property(self :: PROPERTY_DISPLAY_TYPE);
    }

    public function set_order_limit($order_limit)
    {
        return $this->set_additional_property(self :: PROPERTY_ORDER_LIMIT, $order_limit);
    }

    public function get_order_limit()
    {
        return $this->get_additional_property(self :: PROPERTY_ORDER_LIMIT);
    }

    public function get_question()
    {
        return $this->get_additional_property(self :: PROPERTY_QUESTION);
    }

    public function set_question($question)
    {
        return $this->set_additional_property(self :: PROPERTY_QUESTION, $question);
    }

    public function get_instruction()
    {
        return $this->get_additional_property(self :: PROPERTY_INSTRUCTION);
    }

    public function set_instruction($instruction)
    {
        return $this->set_additional_property(self :: PROPERTY_INSTRUCTION, $instruction);
    }

    public function has_instruction()
    {
        $instruction = $this->get_instruction();
        return ($instruction != '<p>&#160;</p>' && count($instruction) > 0);
    }

    /**
     * Returns the dependencies for this dataclass
     * 
     * @return string[string]
     */
    protected function get_dependencies()
    {
        $dependencies = parent :: get_dependencies();
        $dependencies[OrderOption :: class_name()] = new EqualityCondition(
            new PropertyConditionVariable(
                OrderOption :: class_name(), 
                OrderOption :: PROPERTY_QUESTION_ID), 
            new StaticConditionVariable($this->get_id()));
        
        return $dependencies;
    }
}