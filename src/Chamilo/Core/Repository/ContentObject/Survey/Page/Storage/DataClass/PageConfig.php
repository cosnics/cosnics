<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass;

use Chamilo\Libraries\Architecture\ClassnameUtilities;

class PageConfig
{
    use\Chamilo\Libraries\Architecture\Traits\ClassContext;
    
    const CLASS_NAME = __CLASS__;
    const PROPERTY_ID = 'id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_FROM_VISIBLE_QUESTION_ID = 'from_visible_question_id';
    const PROPERTY_TO_VISIBLE_QUESTION_IDS = 'to_visible_question_ids';
    const PROPERTY_ANSWER_MATCHES = 'answer_matches';
    const PROPERTY_CONFIG_CREATED = 'config_created';
    const PROPERTY_CONFIG_UPDATED = 'config_updated';

    private $property_values = array();

    function __construct($config)
    {
        $this->set_id($config[self :: PROPERTY_ID]);
        $this->set_answer_matches($config[self :: PROPERTY_ANSWER_MATCHES]);
        $this->set_config_created($config[self :: PROPERTY_CONFIG_CREATED]);
        $this->set_config_updated($config[self :: PROPERTY_CONFIG_UPDATED]);
        $this->set_description($config[self :: PROPERTY_DESCRIPTION]);
        $this->set_from_visible_question_id($config[self :: PROPERTY_FROM_VISIBLE_QUESTION_ID]);
        $this->set_name($config[self :: PROPERTY_NAME]);
        $this->set_to_visible_question_ids($config[self :: PROPERTY_TO_VISIBLE_QUESTION_IDS]);
    }

    /**
     * Get the default properties
     * 
     * @return array The property names.
     */
    static function get_property_value_names()
    {
        return parent :: property_values_names(
            array(
                self :: PROPERTY_ID, 
                self :: PROPERTY_NAME, 
                self :: PROPERTY_DESCRIPTION, 
                self :: PROPERTY_FROM_VISIBLE_QUESTION_ID, 
                self :: PROPERTY_TO_VISIBLE_QUESTION_IDS, 
                self :: PROPERTY_ANSWER_MATCHES, 
                self :: PROPERTY_CONFIG_CREATED, 
                self :: PROPERTY_CONFIG_UPDATED));
    }

    /**
     * Returns the id of this PageConfig.
     * 
     * @return the id.
     */
    function get_id()
    {
        return $this->property_values[self :: PROPERTY_ID];
    }

    /**
     * Sets the id of this PageConfig.
     * 
     * @param id
     */
    function set_id($id)
    {
        $this->property_values[self :: PROPERTY_ID] = $id;
    }

    /**
     * Sets the name of this PageConfig.
     * 
     * @param name
     */
    function set_name($name)
    {
        $this->property_values[self :: PROPERTY_NAME] = $name;
    }

    /**
     * Returns the name of this PageConfig.
     * 
     * @return the name.
     */
    function get_name()
    {
        return $this->property_values[self :: PROPERTY_NAME];
    }

    /**
     * Returns the description of this PageConfig.
     * 
     * @return the description.
     */
    function get_description()
    {
        return $this->property_values[self :: PROPERTY_DESCRIPTION];
    }

    /**
     * Sets the description of this PageConfig.
     * 
     * @param from_visible_question_id
     */
    function set_description($description)
    {
        $this->property_values[self :: PROPERTY_DESCRIPTION] = $description;
    }

    function get_from_visible_question_id()
    {
        return $this->property_values[self :: PROPERTY_FROM_VISIBLE_QUESTION_ID];
    }

    /**
     * Sets the description of this PageConfig.
     * 
     * @param from_visible_question_id
     */
    function set_from_visible_question_id($from_visible_question_id)
    {
        $this->property_values[self :: PROPERTY_FROM_VISIBLE_QUESTION_ID] = $from_visible_question_id;
    }

    /**
     * Returns the to_visible_question_ids of this PageConfig.
     * 
     * @return the to_visible_question_ids.
     */
    function get_to_visible_question_ids()
    {
        return $this->property_values[self :: PROPERTY_TO_VISIBLE_QUESTION_IDS];
    }

    /**
     * Sets the to_visible_question_ids of this PageConfig.
     * 
     * @param to_visible_question_ids
     */
    function set_to_visible_question_ids($to_visible_question_ids)
    {
        $this->property_values[self :: PROPERTY_TO_VISIBLE_QUESTION_IDS] = $to_visible_question_ids;
    }

    /**
     * Returns the answer_matches of this PageConfig.
     * 
     * @return the answer_matches.
     */
    function get_answer_matches()
    {
        return $this->property_values[self :: PROPERTY_ANSWER_MATCHES];
    }

    /**
     * Sets the answer_matches of this PageConfig.
     * 
     * @param answer_matches
     */
    function set_answer_matches($answer_matches)
    {
        $this->property_values[self :: PROPERTY_ANSWER_MATCHES] = $answer_matches;
    }

    /**
     * Returns the config_created of this PageConfig.
     * 
     * @return the config_created.
     */
    function get_config_created()
    {
        return $this->property_values[self :: PROPERTY_CONFIG_CREATED];
    }

    /**
     * Sets the config_created of this PageConfig.
     * 
     * @param config_created
     */
    function set_config_created($config_created)
    {
        $this->property_values[self :: PROPERTY_CONFIG_CREATED] = $config_created;
    }

    /**
     * Returns the config_updated of this PageConfig.
     * 
     * @return the config_updated.
     */
    function get_config_updated()
    {
        return $this->property_values[self :: PROPERTY_CONFIG_UPDATED];
    }

    /**
     * Sets the config_updated of this PageConfig.
     * 
     * @param config_updated
     */
    function set_config_updated($config_updated)
    {
        $this->property_values[self :: PROPERTY_CONFIG_UPDATED] = $config_updated;
    }

    public static function context()
    {
        return ClassnameUtilities :: getInstance()->getNamespaceFromClassname(__CLASS__);
    }
}

?>