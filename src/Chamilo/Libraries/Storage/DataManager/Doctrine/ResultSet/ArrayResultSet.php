<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ResultSet;

use PDO;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ResultSet
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Use \ArrayIterator now
 */
class ArrayResultSet extends \Chamilo\Libraries\Storage\ResultSet\ArrayResultSet
{

    /**
     * Create a new DoctrineResultSet for handling a set of records
     *
     * @param \Doctrine\DBAL\Driver\PDOStatement $handle
     */
    public function __construct($handle)
    {
        $data = array();

        while ($record = $handle->fetch(PDO::FETCH_ASSOC))
        {
            $data[] = $this->process_record($record);
        }

        parent::__construct($data);
    }
}
