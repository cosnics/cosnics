<?php
namespace Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;

/**
 *
 * @package repository.lib.content_object.evaluation
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */

/**
 * A Evaluation
 */
class Evaluation extends ContentObject implements Versionable
{
    const PROPERTY_RUBRIC_ID = 'rubric_id';

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }

    public static function get_additional_property_names()
    {
        $propertyNames = parent::get_additional_property_names();
        $propertyNames[] = self::PROPERTY_RUBRIC_ID;
        return $propertyNames;
    }

    /**
     * @return int
     */
    public function getRubricId(): ?int // -> contentobject id
    {
        return $this->get_additional_property(self::PROPERTY_RUBRIC_ID);
    }

    /**
     * @param ?int $rubricId
     *
     * @return $this
     */
    public function setRubricId(?int $rubricId): Evaluation
    {
        $this->set_additional_property(self::PROPERTY_RUBRIC_ID, $rubricId);

        return $this;
    }
}
