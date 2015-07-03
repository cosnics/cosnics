<?php
namespace Chamilo\Application\CasUser\Account\Storage;

/**
 *
 * @author Hans De Bisschop
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{

    public static $instance;

    public static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new \Chamilo\Application\CasUser\Account\Storage\Database();
        }
        return self :: $instance;
    }
}
