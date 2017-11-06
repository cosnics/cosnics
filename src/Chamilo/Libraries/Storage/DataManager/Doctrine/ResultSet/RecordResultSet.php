<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ResultSet;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ResultSet
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Use RecordIterator now
 */
class RecordResultSet extends \Chamilo\Libraries\Storage\ResultSet\RecordResultSet
{

    /**
     *
     * @param \Doctrine\DBAL\Driver\PDOStatement $handle
     */
    public function __construct($handle)
    {
        $records = array();

        while ($record = $handle->fetch(\PDO::FETCH_ASSOC))
        {
            $records[] = $this->process_record($record);
        }

        parent::__construct($records);
    }
}
