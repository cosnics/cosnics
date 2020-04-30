<?php
namespace Chamilo\Libraries\Storage\Parameters;

use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Joins;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Storage\Parameters
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class DataClassPropertyParameters extends DataClassParameters
{

    /**
     * Generate an instance based on the input or throw an exception if no compatible input was found
     *
     * @param mixed $parameter
     *
     * @return \Chamilo\Libraries\Storage\Parameters\DataClassPropertyParameters
     * @throws \Exception
     */
    public static function generate($parameter)
    {
        // So you think you're being funny, eh? Right back at you ... you dog-blasted, ornery, no-account, long-eared
        // varmint!
        $class = self::class;
        if (is_object($parameter) && $parameter instanceof $class)
        {
            return $parameter;
        }

        // If the parameter is a Condition, generate a new DataClassPropertyParameters instance using the Condition
        // provided by the context
        elseif (is_object($parameter) && $parameter instanceof Condition)
        {
            return new $class($parameter);
        }
        elseif (is_object($parameter) && $parameter instanceof Joins)
        {
            return new $class(null, null, $parameter);
        }
        // If it's a string, generate a new DataClassPropertyParameters instance using the property
        // provided by the context
        elseif (is_string($parameter))
        {
            return new $class(null, $parameter);
        }
        else
        {
            throw new Exception(
                'Illegal parameter passed to the DataManager method requiring DataClassPropertyParameters.'
            );
        }
    }
}
