<?php
namespace Chamilo\Application\CasUser\Account\Storage;

class Database extends \Chamilo\Libraries\Storage\DataManager\Doctrine\Database

{

    public function __construct($aliases = array())
    {
        parent :: __construct($aliases, Connection :: get_instance()->get_connection());
    }
}
