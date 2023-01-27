<?php
namespace Chamilo\Libraries\Architecture\Traits;

use Chamilo\Libraries\Architecture\ClassnameUtilities;

/**
 *
 * @package Chamilo\Libraries\Architecture\Traits
 */
trait ClassContext
{
    /**
     * @throws \ReflectionException
     * @deprecated Replace with the static constant Class::CONTEXT
     */
    public static function context(): string
    {
        return ClassnameUtilities::getInstance()->getNamespaceFromClassname(get_called_class());
    }
}
