<?php
namespace Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;

/**
 *
 * @package repository.lib.content_object.presence
 */
/**
 * This class represents a presence
 */
class Presence extends ContentObject implements Versionable
{

    const PROPERTY_OPTIONS = 'options';

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }

    public static function get_additional_property_names()
    {
        $propertyNames = parent::get_additional_property_names();
        $propertyNames[] = self::PROPERTY_OPTIONS;
        return $propertyNames;
    }

    /**
     * @return string
     */
    public function getOptions(): string
    {
        return $this->get_additional_property(self::PROPERTY_OPTIONS);
    }

    /**
     * @param string $options
     *
     * @return $this
     */
    public function setOptions(string $options): Presence
    {
        $this->set_additional_property(self::PROPERTY_OPTIONS, $options);
        return $this;
    }
}