<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Storage\DataClass
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class UserOvertime extends DataClass
{
    const PROPERTY_PUBLICATION_ID = 'publication_id';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_EXTRA_TIME = 'extra_time';

    /**
     * Get the default properties
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return DataClass::get_default_property_names(
            array(
                self::PROPERTY_PUBLICATION_ID,
                self::PROPERTY_USER_ID,
                self::PROPERTY_EXTRA_TIME
            )
        );
    }

    /**
     * @return int
     */
    public function getPublicationId()
    {
        return (int) $this->get_default_property(self::PROPERTY_PUBLICATION_ID);
    }

    /**
     * @param int $publicationId
     */
    public function setPublicationId(int $publicationId)
    {
        $this->set_default_property(self::PROPERTY_PUBLICATION_ID, $publicationId);
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return (int) $this->get_default_property(self::PROPERTY_USER_ID);
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $userId);
    }

    /**
     * @return int
     */
    public function getExtraTime()
    {
        return (int) $this->get_default_property(self::PROPERTY_EXTRA_TIME);
    }

    /**
     * @param int $extraTime
     */
    public function setExtraTime(int $extraTime)
    {
        $this->set_default_property(self::PROPERTY_EXTRA_TIME, $extraTime);
    }

    public static function get_table_name()
    {
        return 'weblcms_exam_assignment_user_overtime';
    }
}
