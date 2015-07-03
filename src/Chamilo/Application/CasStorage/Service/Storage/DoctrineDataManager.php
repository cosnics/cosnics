<?php
namespace Chamilo\Application\CasStorage\Service\Storage;

use Chamilo\Application\CasStorage\Service\Storage\Connection\DoctrineConnection;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Database;

class DoctrineDataManager extends Database
{

    /**
     * Initialiser, creates the connection and sets the database to UTF8
     */
    public function initialize()
    {
        $this->set_connection(DoctrineConnection :: get_instance()->get_connection());
        $this->get_connection()->setCharset('utf8');
        $this->set_prefix('');
    }
}
