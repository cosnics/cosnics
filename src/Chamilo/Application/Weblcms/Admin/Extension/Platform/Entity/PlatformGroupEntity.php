<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity;

class PlatformGroupEntity extends \Chamilo\Core\Rights\Entity\PlatformGroupEntity
{

    /**
     * Get the fully qualified class name of the object
     * 
     * @return string
     */
    public static function class_name()
    {
        return get_called_class();
    }
}
