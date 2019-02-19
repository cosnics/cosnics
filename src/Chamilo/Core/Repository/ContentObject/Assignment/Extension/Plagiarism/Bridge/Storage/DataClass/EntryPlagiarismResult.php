<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass;

use Chamilo\Application\Plagiarism\Domain\SubmissionStatus;
use Chamilo\Application\Plagiarism\Storage\DataClass\PlagiarismResult;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Storage\DataClass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class EntryPlagiarismResult extends PlagiarismResult
{
    const PROPERTY_ENTRY_ID = 'entry_id';

    /**
     * Get the default properties of all feedback
     *
     * @param array $extendedPropertyNames
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extendedPropertyNames = array())
    {
        $extendedPropertyNames[] = self::PROPERTY_ENTRY_ID;

        return parent::get_default_property_names($extendedPropertyNames);
    }

    /**
     * @return int
     */
    public function getEntryId()
    {
        return $this->get_default_property(self::PROPERTY_ENTRY_ID);
    }

    /**
     * @param int $entryId
     */
    public function setEntryId($entryId)
    {
        $this->set_default_property(self::PROPERTY_ENTRY_ID, $entryId);
    }
}