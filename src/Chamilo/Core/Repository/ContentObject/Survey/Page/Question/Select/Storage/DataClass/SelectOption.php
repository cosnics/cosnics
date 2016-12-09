<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Select\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Select\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package repository.content_object.survey_select_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
/**
 * This class represents an option in a select question.
 */
class SelectOption extends DataClass
{
    const PROPERTY_VALUE = 'option_value';
    const PROPERTY_QUESTION_ID = 'question_id';
    const PROPERTY_DISPLAY_ORDER = 'display_order';

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
}


