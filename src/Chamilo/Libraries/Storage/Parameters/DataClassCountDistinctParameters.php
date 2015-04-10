<?php
namespace Chamilo\Libraries\Storage\Parameters;

use Exception;

/**
 *
 * @package Chamilo\Libraries\Storage\Parameters
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataClassCountDistinctParameters extends DataClassPropertyParameters
{

    /**
     * Throw an exception if the DataClassPropertyParameters object is invalid
     *
     * @throws \Exception
     */
    public static function invalid()
    {
        throw new Exception('Illegal parameter(s) passed to the DataManager :: count_distinct() method.');
    }
}
