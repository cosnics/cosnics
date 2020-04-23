<?php
namespace Chamilo\Libraries\Architecture\Traits;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Libraries\Architecture\Traits
 */
trait ClassContext
{

    /**
     * Get the fully qualified class name of the object instance
     *
     * @param boolean $fullyQualified
     * @param boolean $camelCase
     *
     * @return string
     * @deprecated Use ClassName::class or static::class now
     * @throws \ReflectionException
     */
    public static function class_name($fullyQualified = true, $camelCase = true)
    {
        if (!$fullyQualified)
        {
            return ClassnameUtilities::getInstance()->getClassNameFromNamespace(get_called_class(), !$camelCase);
        }

        if ($camelCase)
        {
            return get_called_class();
        }

        return (string) StringUtilities::getInstance()->createString(get_called_class())->underscored();
    }

    /**
     * Get the namespace of the object instance
     *
     * @return string
     * @throws \ReflectionException
     */
    public static function context()
    {
        return ClassnameUtilities::getInstance()->getNamespaceFromClassname(get_called_class());
    }
}
