<?php
namespace Chamilo\Libraries\Storage\DataManager\Mdb2;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Mdb2
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataSourceName extends \Chamilo\Libraries\Storage\DataManager\DataSourceName
{

    /**
     *
     * @see \Chamilo\Libraries\Storage\DataManager\DataSourceName::get_implemented_driver()
     */
    public function get_implemented_driver()
    {
        switch ($this->get_driver())
        {
            case self :: DRIVER_OCI8 :
                return self :: DRIVER_OCI8;
                break;
            case self :: DRIVER_PGSQL :
                return self :: DRIVER_PGSQL;
                break;
            case self :: DRIVER_SQLITE :
                return self :: DRIVER_SQLITE;
                break;
            case self :: DRIVER_MYSQL :
                return 'mysqli';
                break;
            case self :: DRIVER_MSSQL :
                return 'mssql';
                break;
            case self :: DRIVER_INTERBASE :
                return 'ibase';
                break;
            // Deprecated option for backwards compatibility with older configuration files
            case 'mysqli' :
                return 'mysqli';
                break;
            default :
                throw new \Exception(
                    'The requested driver (' . $this->get_driver() .
                         ') is not available in Mdb2. Please provide a driver for Mdb2 or choose another implementation');
                break;
        }
    }
}
