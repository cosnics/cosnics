<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Storage\DataClass;

use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Storage\DataClass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Publication extends \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataClass\Publication
{
    const PROPERTY_CODE = 'code';
    const PROPERTY_FEEDBACK_FROM_DATE = 'feedback_from_date';
    const PROPERTY_FEEDBACK_TO_DATE = 'feedback_to_date';

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
                self::PROPERTY_CODE,
                self::PROPERTY_FEEDBACK_FROM_DATE,
                self::PROPERTY_FEEDBACK_TO_DATE
            )
        );
    }

    /**
     * @return int
     */
    public function getFromDate()
    {
        return (int) $this->get_default_property(self::PROPERTY_FEEDBACK_FROM_DATE);
    }

    /**
     * @param string $date
     */
    public function setFromDate(string $date)
    {
        $this->set_default_property(self::PROPERTY_FEEDBACK_FROM_DATE, $date);
    }

    /**
     * @return int
     */
    public function getToDate()
    {
        return (int) $this->get_default_property(self::PROPERTY_FEEDBACK_TO_DATE);
    }

    /**
     * @param string $date
     */
    public function setToDate(string $date)
    {
        $this->set_default_property(self::PROPERTY_FEEDBACK_TO_DATE, $date);
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return (int) $this->get_default_property(self::PROPERTY_CODE);
    }

    /**
     * @param User $user
     *
     * @return string
     */
    public function getSecurityCode(User $user)
    {
        return md5($this->getPublicationId() . '-' . $this->getCode() . '-' . $user->getId());
    }

    /**
     * @param string|null $code
     */
    public function setCode(string $code = null)
    {
        $this->set_default_property(self::PROPERTY_CODE, $code);
    }

    public function getEntityType()
    {
        return Entry::ENTITY_TYPE_USER;
    }

    public function getCheckForPlagiarism()
    {
        return false;
    }

    public static function get_table_name()
    {
        return 'weblcms_exam_assignment_publication';
    }
}

