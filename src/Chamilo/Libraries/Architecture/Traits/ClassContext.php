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
     * @throws \ReflectionException
     *
     * @deprecated Use ClassName::class or static::class now
     */
    public static function class_name(bool $fullyQualified = true, bool $camelCase = true): string
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
     * @throws \ReflectionException
     */
    public static function context(): string
    {
        return ClassnameUtilities::getInstance()->getNamespaceFromClassname(get_called_class());
    }
}
