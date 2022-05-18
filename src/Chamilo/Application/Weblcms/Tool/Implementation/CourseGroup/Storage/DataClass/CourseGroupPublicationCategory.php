<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Defines the relation between a CourseGroup and a ContentObjectPublicationCategory
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupPublicationCategory extends DataClass
{
    const PROPERTY_COURSE_GROUP_ID = 'course_group_id';
    const PROPERTY_PUBLICATION_CATEGORY_ID = 'publication_category_id';

    /**
     * @return int
     */
    public function getCourseGroupId()
    {
        return $this->getDefaultProperty(self::PROPERTY_COURSE_GROUP_ID);
    }

    /**
     * @return int
     */
    public function getPublicationCategoryId()
    {
        return $this->getDefaultProperty(self::PROPERTY_PUBLICATION_CATEGORY_ID);
    }

    /**
     * @return string[]
     */
    public static function getDefaultPropertyNames($extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [self::PROPERTY_COURSE_GROUP_ID, self::PROPERTY_PUBLICATION_CATEGORY_ID]
        );
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'weblcms_course_group_publication_category';
    }

    /**
     * @param int $courseGroupId
     *
     * @return $this
     */
    public function setCourseGroupId($courseGroupId)
    {
        $this->setDefaultProperty(self::PROPERTY_COURSE_GROUP_ID, $courseGroupId);

        return $this;
    }

    /**
     * @param int $contentObjectPublicationCategoryId
     *
     * @return $this
     */
    public function setPublicationCategoryId($contentObjectPublicationCategoryId)
    {
        $this->setDefaultProperty(
            self::PROPERTY_PUBLICATION_CATEGORY_ID, $contentObjectPublicationCategoryId
        );

        return $this;
    }
}