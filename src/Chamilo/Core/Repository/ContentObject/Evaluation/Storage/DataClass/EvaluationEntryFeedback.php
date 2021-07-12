<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass;

use Chamilo\Core\Repository\Feedback\PrivateFeedbackSupport;
use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class EvaluationEntryFeedback extends Feedback implements PrivateFeedbackSupport
{
    const PROPERTY_ENTRY_ID = 'entry_id';
    const PROPERTY_IS_PRIVATE = 'is_private';

    /**
     *
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function get_default_property_names($extendedPropertyNames = array())
    {
        return parent::get_default_property_names(array(self::PROPERTY_ENTRY_ID, self::PROPERTY_IS_PRIVATE));
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

    /**
     * @return bool
     */
    public function isPrivate(): bool
    {
        return $this->get_default_property(self::PROPERTY_IS_PRIVATE);
    }

    /**
     * @param bool $isPrivate
     */
    public function setIsPrivate(bool $isPrivate): void
    {
        $this->set_default_property(self::PROPERTY_IS_PRIVATE, $isPrivate);
    }

    public static function get_table_name()
    {
        return 'repository_evaluation_entry_feedback';
    }
}

