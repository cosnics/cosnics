<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListener;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListenerSupport;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * This class represents an option in a matrix question.
 * 
 * @package repository.content_object.survey_matrix_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class MatrixOption extends DataClass implements DisplayOrderDataClassListenerSupport
{
    const PROPERTY_VALUE = 'option_value';
    const PROPERTY_QUESTION_ID = 'question_id';
    const PROPERTY_DISPLAY_ORDER = 'display_order';

    public function __construct($default_properties = array(), $optional_properties = array())
    {
        parent::__construct($default_properties, $optional_properties);
        $this->add_listener(new DisplayOrderDataClassListener($this));
    }

    /**
     * Get the default properties
     * 
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent::get_default_property_names(
            array(self::PROPERTY_VALUE, self::PROPERTY_QUESTION_ID, self::PROPERTY_DISPLAY_ORDER));
    }

    function get_data_manager()
    {
        return DataManager::getInstance();
    }

    /**
     * Gets the value of this option
     * 
     * @return string
     */
    function get_value()
    {
        return $this->get_default_property(self::PROPERTY_VALUE);
    }

    /**
     * Sets the value of this option
     * 
     * @return string
     */
    function set_value($value)
    {
        return $this->set_default_property(self::PROPERTY_VALUE, $value);
    }

    /**
     * Gets the question_id of this option
     * 
     * @return int
     */
    function get_question_id()
    {
        return $this->get_default_property(self::PROPERTY_QUESTION_ID);
    }

    /**
     * Sets the question_id of this option
     * 
     * @return int
     */
    function set_question_id($question_id)
    {
        return $this->set_default_property(self::PROPERTY_QUESTION_ID, $question_id);
    }

    function get_display_order()
    {
        return $this->get_default_property(self::PROPERTY_DISPLAY_ORDER);
    }

    function set_display_order($display_order)
    {
        $this->set_default_property(self::PROPERTY_DISPLAY_ORDER, $display_order);
    }

    /**
     * Returns the property for the display order
     * 
     * @return string
     */
    public function get_display_order_property()
    {
        return new PropertyConditionVariable(self::class_name(), self::PROPERTY_DISPLAY_ORDER);
    }

    /**
     * Returns the properties that define the context for the display order (the properties on which has to be limited)
     * 
     * @return Condition
     */
    public function get_display_order_context_properties()
    {
        return array(new PropertyConditionVariable(self::class_name(), self::PROPERTY_QUESTION_ID));
    }
}