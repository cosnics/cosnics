<?php
namespace Chamilo\Application\CasStorage\Account\Storage;

class Database extends \Chamilo\Libraries\Storage\DataManager\Doctrine\Database

{

    public function __construct($aliases = array())
    {
        parent :: __construct($aliases, Connection :: getInstance()->get_connection());
    }
}
