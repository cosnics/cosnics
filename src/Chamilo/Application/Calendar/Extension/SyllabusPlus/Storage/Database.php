<?php
namespace Chamilo\Application\Calendar\Extension\SyllabusPlus\Storage;

class Database extends \Chamilo\Libraries\Storage\DataManager\Doctrine\Database
{

    /**
     * Initialiser, creates the connection and sets the database to UTF8
     */
    public function __construct($connection = null)
    {
        parent :: __construct(Connection :: get_instance()->get_connection());
        $this->get_connection()->query('SET TEXTSIZE 2000000');
    }
}
