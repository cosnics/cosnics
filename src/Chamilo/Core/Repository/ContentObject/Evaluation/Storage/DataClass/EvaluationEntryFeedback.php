<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class EvaluationEntryFeedback extends \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback
{
    const PROPERTY_ENTRY_ID = 'entry_id';

    /**
     *
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function get_default_property_names($extendedPropertyNames = array())
    {
        return parent::get_default_property_names(array(self::PROPERTY_ENTRY_ID));
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

