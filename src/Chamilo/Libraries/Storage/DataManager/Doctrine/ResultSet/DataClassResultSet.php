<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ResultSet;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ResultSet
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Use DataClassIterator now
 */
class DataClassResultSet extends \Chamilo\Libraries\Storage\ResultSet\DataClassResultSet
{

    /**
     * Create a new DoctrineResultSet for handling a set of records
     *
     * @param \Doctrine\DBAL\Driver\PDOStatement $handle
     * @param string $className
     */
    public function __construct($handle, $className)
    {
        $objects = array();

        while ($record = $handle->fetch(\PDO::FETCH_ASSOC))
        {
            $objects[] = $this->get_object($className, $this->process_record($record));
        }

        parent::__construct($className, $objects);
    }
}
