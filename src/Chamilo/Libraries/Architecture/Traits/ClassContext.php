<?php
namespace Chamilo\Libraries\Architecture\Traits;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

trait ClassContext
{

    /**
     * Get the fully qualified class name of the object instance
     *
     * @return string
     */
    public static function class_name($fully_qualified = true, $camel_case = true)
    {
        if (! $fully_qualified)
        {
            return ClassnameUtilities::getInstance()->getClassNameFromNamespace(get_called_class(), ! $camel_case);
        }

        if ($camel_case)
        {
            return get_called_class();
        }

        return (string) StringUtilities::getInstance()->createString(get_called_class())->underscored();
    }

    /**
     * Get the namespace of the object instance
     *
     * @return string
     */
    public static function context()
    {
        return ClassnameUtilities::getInstance()->getNamespaceFromClassname(get_called_class());
    }
}
