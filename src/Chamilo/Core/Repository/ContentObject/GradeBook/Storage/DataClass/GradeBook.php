<?php
namespace Chamilo\Core\Repository\ContentObject\GradeBook\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;

/**
 *
 * @package repository.lib.content_object.gradebook
 */
/**
 * This class represents a gradebook
 */
class GradeBook extends ContentObject implements Versionable
{
    const PROPERTY_ACTIVE_GRADEBOOK_DATA_ID = 'active_gradebook_data_id';

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }

    public static function get_additional_property_names()
    {
        $propertyNames = parent::get_additional_property_names();
        $propertyNames[] = self::PROPERTY_ACTIVE_GRADEBOOK_DATA_ID;
        return $propertyNames;
    }

    /**
     * @return int
     */
    public function getActiveGradeBookDataId()
    {
        return $this->get_additional_property(self::PROPERTY_ACTIVE_GRADEBOOK_DATA_ID);
    }

    /**
     * @param int $activeGradeBookDataId
     *
     * @return $this
     */
    public function setActiveGradeBookDataId(int $activeGradeBookDataId)
    {
        $this->set_additional_property(self::PROPERTY_ACTIVE_GRADEBOOK_DATA_ID, $activeGradeBookDataId);

        return $this;
    }

    public static function get_table_name(): string
    {
        return 'repository_gradebook';
    }
}