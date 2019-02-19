<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Storage\DataClass;

use Chamilo\Application\Plagiarism\Storage\DataClass\PlagiarismResult;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Storage\DataClass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectPlagiarismResult extends PlagiarismResult
{
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    const PROPERTY_COURSE_ID = 'course_id';

    /**
     * Get the default properties of all feedback
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extendedPropertyNames = array())
    {
        $extendedPropertyNames[] = self::PROPERTY_CONTENT_OBJECT_ID;
        $extendedPropertyNames[] = self::PROPERTY_COURSE_ID;

        return parent::get_default_property_names($extendedPropertyNames);
    }

    /**
     * @return int
     */
    public function getContentObjectId()
    {
        return $this->get_default_property(self::PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     * @param int $contentObjectId
     */
    public function setContentObjectId($contentObjectId)
    {
        $this->set_default_property(self::PROPERTY_CONTENT_OBJECT_ID, $contentObjectId);
    }

    /**
     * @return int
     */
    public function getCourseId()
    {
        return $this->get_default_property(self::PROPERTY_COURSE_ID);
    }

    /**
     * @param int $courseId
     */
    public function setCourseId($courseId)
    {
        $this->set_default_property(self::PROPERTY_COURSE_ID, $courseId);
    }

}