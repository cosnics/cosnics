<?php
namespace Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class EvaluationEntryScore extends DataClass
{
    const PROPERTY_EVALUATOR_ID = 'evaluator_id';
    const PROPERTY_SCORE = 'score';
    const PROPERTY_IS_ABSENT = 'is_absent';
    const PROPERTY_CREATED_TIME = 'created_time';
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
                self::PROPERTY_SCORE,
                self::PROPERTY_IS_ABSENT,
                self::PROPERTY_CREATED_TIME,
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
     * @return string
     */
    public function getScore()
    {
        return $this->get_default_property(self::PROPERTY_SCORE);
    }

    /**
     * @param string $score
     */
    public function setScore($score)
    {
        $this->set_default_property(self::PROPERTY_SCORE, $score);
    }

    /**
     * @return bool
     */
    public function isAbsent()
    {
        return $this->get_default_property(self::PROPERTY_IS_ABSENT);
    }

    /**
     * @param bool $isAbsent
     */
    public function setIsAbsent($isAbsent)
    {
        $this->set_default_property(self::PROPERTY_IS_ABSENT, $isAbsent);
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
        return 'repository_evaluation_entry_score';
    }
}

