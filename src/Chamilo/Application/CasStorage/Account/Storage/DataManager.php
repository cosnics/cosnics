<?php
namespace Chamilo\Application\CasStorage\Account\Storage;

/**
 *
 * @author Hans De Bisschop
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{

    public static $instance;

    public static function getInstance()
    {
        if (! isset(self::$instance))
        {
            self::$instance = new \Chamilo\Application\CasStorage\Account\Storage\Database();
        }
        return self::$instance;
    }
}
