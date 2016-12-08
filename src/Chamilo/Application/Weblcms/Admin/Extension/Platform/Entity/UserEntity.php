<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity;

class UserEntity extends \Chamilo\Core\Rights\Entity\UserEntity
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
