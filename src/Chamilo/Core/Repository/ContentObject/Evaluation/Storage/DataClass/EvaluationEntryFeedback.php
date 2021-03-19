<?php
namespace Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class EvaluationEntryFeedback extends DataClass
{
    const PROPERTY_EVALUATOR_ID = 'evaluator_id';
    const PROPERTY_FEEDBACK_CONTENT_OBJECT_ID = 'feedback_content_object_id';
    const PROPERTY_CREATED_TIME = 'created_time';
    const PROPERTY_MODIFIED_TIME = 'modified_time';
    const PROPERTY_ENTRY_ID = 'entry_id';

    /**
     * Get the default properties
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_EVALUATOR_ID,
                self::PROPERTY_FEEDBACK_CONTENT_OBJECT_ID,
                self::PROPERTY_CREATED_TIME,
                self::PROPERTY_MODIFIED_TIME,
                self::PROPERTY_ENTRY_ID
            )
        );
    }

    /**
     * @return int
     */
    public function getEvaluatorId()
    {
        return $this->get_default_property(self::PROPERTY_EVALUATOR_ID);
    }

    /**
     * @param int $evaluatorId
     */
    public function setEvaluatorId($evaluatorId)
    {
        $this->set_default_property(self::PROPERTY_EVALUATOR_ID, $evaluatorId);
    }

    /**
     * @return int
     */
    public function getFeedbackContentObjectId()
    {
        return $this->get_default_property(self::PROPERTY_FEEDBACK_CONTENT_OBJECT_ID);
    }

    /**
     * @param int $feedbackContentObjectId
     */
    public function setFeedbackContentObjectId($feedbackContentObjectId)
    {
        $this->set_default_property(self::PROPERTY_FEEDBACK_CONTENT_OBJECT_ID, $feedbackContentObjectId);
    }

    /**
     * @return int
     */
    public function getCreatedTime()
    {
        return $this->get_default_property(self::PROPERTY_CREATED_TIME);
    }

    /**
     * @param int $created_time
     */
    public function setCreatedTime($created_time)
    {
        $this->set_default_property(self::PROPERTY_CREATED_TIME, $created_time);
    }

    /**
     * @return int
     */
    public function getModifiedTime()
    {
        return $this->get_default_property(self::PROPERTY_MODIFIED_TIME);
    }

    /**
     * @param int $modified_time
     */
    public function setModifiedTime($modified_time)
    {
        $this->set_default_property(self::PROPERTY_MODIFIED_TIME, $modified_time);
    }

    /**
     * @return int
     */
    public function getEntryId()
    {
        return $this->get_default_property(self::PROPERTY_ENTRY_ID);
    }

    /**
     * @param int $entry_id
     */
    public function setEntryId($entry_id)
    {
        $this->set_default_property(self::PROPERTY_ENTRY_ID, $entry_id);
    }

    public static function get_table_name()
    {
        return 'repository_evaluation_entry_feedback';
    }
}

