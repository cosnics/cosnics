<?php
namespace Chamilo\Libraries\Storage\DataManager\Mdb2\ResultSet;

use Chamilo\Libraries\Storage\ResultSet\DataClassResultSet;

/**
 * This class represents an MDB2 object ResultSet
 * 
 * @package common.libraries
 * @author Hans De Bisschop
 */
class DataClassResultSet extends DataClassResultSet
{

    /**
     * Create a new Mdb2ResultSet for handling a set of records
     * 
     * @param $data_manager Mdb2Database
     * @param $handle MDB2_Result_Common
     * @param $class_name string
     */
    public function __construct($data_manager, $handle, $class_name)
    {
        $objects = array();
        
        while ($record = $handle->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $objects[] = $this->get_object($class_name, $this->process_record($record));
        }
        
        parent::__construct($class_name, $objects);
    }
}
