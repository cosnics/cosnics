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
    const PROPERTY_REQUEST_USER_ID = 'request_user_id';
    const PROPERTY_REQUEST_DATE = 'request_date';

    /**
     * Get the default properties of all feedback
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extendedPropertyNames = array())
    {
        $extendedPropertyNames[] = self::PROPERTY_CONTENT_OBJECT_ID;
        $extendedPropertyNames[] = self::PROPERTY_COURSE_ID;
        $extendedPropertyNames[] = self::PROPERTY_REQUEST_USER_ID;
        $extendedPropertyNames[] = self::PROPERTY_REQUEST_DATE;

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

    /**
     * @return int
     */
    public function getRequestUserId()
    {
        return $this->get_default_property(self::PROPERTY_REQUEST_USER_ID);
    }

    /**
     * @return \DateTime
     */
    public function getRequestDate()
    {
        $timestamp = $this->get_default_property(self::PROPERTY_REQUEST_DATE);
        $dateTime = new \DateTime();
        $dateTime->setTimestamp($timestamp);

        return $dateTime;
    }

    /**
     * @param int $requestUserId
     */
    public function setRequestUserId(int $requestUserId)
    {
        $this->set_default_property(self::PROPERTY_REQUEST_USER_ID, $requestUserId);
    }

    /**
     * @param \DateTime $requestDate
     */
    public function setRequestDate(\DateTime $requestDate)
    {
        $this->set_default_property(self::PROPERTY_REQUEST_DATE, $requestDate->getTimestamp());
    }

}