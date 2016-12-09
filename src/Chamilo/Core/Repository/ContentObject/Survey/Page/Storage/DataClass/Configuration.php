<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListener;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListenerSupport;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

class Configuration extends DataClass implements DisplayOrderDataClassListenerSupport
{
    const PROPERTY_PAGE_ID = 'page_id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_COMPLEX_QUESTION_ID = 'complex_question_id';
    const PROPERTY_TO_VISIBLE_QUESTION_IDS = 'to_visible_question_ids';
    const PROPERTY_ANSWER_MATCHES = 'answer_matches';
    const PROPERTY_CREATED = 'created';
    const PROPERTY_UPDATED = 'updated';
    const PROPERTY_DISPLAY_ORDER = 'display_order';

    public function __construct($default_properties = array(), $additional_properties = null)
    {
        parent::__construct($default_properties, $additional_properties);
        $this->add_listener(new DisplayOrderDataClassListener($this));
    }

    static function get_default_property_names()
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_PAGE_ID, 
                self::PROPERTY_NAME, 
                self::PROPERTY_DESCRIPTION, 
                self::PROPERTY_COMPLEX_QUESTION_ID, 
                self::PROPERTY_TO_VISIBLE_QUESTION_IDS, 
                self::PROPERTY_ANSWER_MATCHES, 
                self::PROPERTY_CREATED, 
                self::PROPERTY_UPDATED, 
                self::PROPERTY_DISPLAY_ORDER));
    }

    /**
     * Sets the pageId of this Configuration.
     * 
     * @param pageId
     */
    function setPageId($pageId)
    {
        $this->set_default_property(self::PROPERTY_PAGE_ID, $pageId);
    }

    /**
     * Returns the name of this Configuration.
     * 
     * @return the pageId.
     */
    function getPageId()
    {
        return $this->get_default_property(self::PROPERTY_PAGE_ID);
    }

    /**
     * Sets the name of this Configuration.
     * 
     * @param name
     */
    function setName($name)
    {
        $this->set_default_property(self::PROPERTY_NAME, $name);
    }

    /**
     * Returns the name of this Configuration.
     * 
     * @return the name.
     */
    function getName()
    {
        return $this->get_default_property(self::PROPERTY_NAME);
    }

    /**
     * Returns the description of this Configuration.
     * 
     * @return the description.
     */
    function getDescription()
    {
        return $this->get_default_property(self::PROPERTY_DESCRIPTION);
    }

    /**
     * Sets the description of this Configuration.
     * 
     * @param from_visible_question_id
     */
    function setDescription($description)
    {
        $this->set_default_property(self::PROPERTY_DESCRIPTION, $description);
    }

    function getComplexQuestionId()
    {
        return $this->get_default_property(self::PROPERTY_COMPLEX_QUESTION_ID);
    }

    /**
     * Sets the description of this Configuration.
     * 
     * @param complex_question_id
     */
    function setComplexQuestionId($complex_question_id)
    {
        $this->set_default_property(self::PROPERTY_COMPLEX_QUESTION_ID, $complex_question_id);
    }

    /**
     * Returns the to_visible_question_ids of this Configuration.
     * 
     * @return the to_visible_question_ids.
     */
    function getToVisibleQuestionIds()
    {
        return unserialize($this->get_default_property(self::PROPERTY_TO_VISIBLE_QUESTION_IDS));
    }

    /**
     * Sets the to_visible_question_ids of this Configuration.
     * 
     * @param to_visible_question_ids
     */
    function setToVisibleQuestionIds($toVisibleQuestionIds)
    {
        $this->set_default_property(self::PROPERTY_TO_VISIBLE_QUESTION_IDS, serialize($toVisibleQuestionIds));
    }

    /**
     * Returns the answer_matches of this Configuration.
     * 
     * @return the answer_matches.
     */
    function getAnswerMatches($prefix = null)
    {
        $answerMatches = unserialize($this->get_default_property(self::PROPERTY_ANSWER_MATCHES));
        if ($prefix)
        {
            
            $prefixAnswerMatches = array();
            foreach ($answerMatches as $answerId => $answerMatch)
            {
                $prefixAnswerMatches[$prefix . '_' . $answerId] = $answerMatch;
            }
            $answerMatches = $prefixAnswerMatches;
        }
        
        return $answerMatches;
    }

    /**
     * Sets the answer_matches of this Configuration.
     * 
     * @param answer_matches
     */
    function setAnswerMatches($answer_matches)
    {
        $this->set_default_property(self::PROPERTY_ANSWER_MATCHES, serialize($answer_matches));
    }

    /**
     * Returns the created of this Configuration.
     * 
     * @return the created.
     */
    function getCreated()
    {
        return $this->get_default_property(self::PROPERTY_CREATED);
    }

    /**
     * Sets the created of this Configuration.
     * 
     * @param created
     */
    function setCreated($created)
    {
        $this->set_default_property(self::PROPERTY_CREATED, $created);
    }

    /**
     * Returns the updated of this Configuration.
     * 
     * @return the updated.
     */
    function getUpdated()
    {
        return $this->get_default_property(self::PROPERTY_UPDATED);
    }

    /**
     * Sets the updated of this Configuration.
     * 
     * @param updated
     */
    function setUpdated($updated)
    {
        $this->set_default_property(self::PROPERTY_UPDATED, $updated);
    }

    public function get_display_order()
    {
        return $this->get_default_property(self::PROPERTY_DISPLAY_ORDER);
    }

    public function set_display_order($display_order)
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
        return new PropertyConditionVariable(Configuration::class_name(), self::PROPERTY_DISPLAY_ORDER);
    }

    /**
     * Returns the properties that define the context for the display order (the properties on which has to be limited)
     * 
     * @return Condition
     */
    public function get_display_order_context_properties()
    {
        return array(new PropertyConditionVariable(Configuration::class_name(), self::PROPERTY_PAGE_ID));
    }
}

?>