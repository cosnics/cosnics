<?php
namespace Chamilo\Core\Menu\Storage;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Exception\DataClassNoResultException;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Core\Menu\Storage
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'menu_';

    public static function retrieve_item($id, $type = null)
    {
        if (! isset($id) || strlen($id) == 0 || $id == DataClass :: NO_UID)
        {
            throw new DataClassNoResultException(
                Item :: class_name(), 
                DataClassRetrieveParameters :: generate((int) $id));
        }
        
        if (is_null($type))
        {
            $type = self :: determine_item_type($id);
        }
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Item :: class_name(), Item :: PROPERTY_ID), 
            new StaticConditionVariable($id));
        
        $parameters = new DataClassRetrieveParameters($condition);
        return self :: fetch_item($parameters, $type);
    }

    /**
     * Get the type of the item matching the given id.
     * 
     * @param int $id The id of the item.
     * @return string The type string.
     */
    public static function determine_item_type($id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Item :: class_name(), Item :: PROPERTY_ID), 
            new StaticConditionVariable($id));
        $parameters = new RecordRetrieveParameters(
            new DataClassProperties(array(new PropertyConditionVariable(Item :: class_name(), Item :: PROPERTY_TYPE))), 
            $condition);
        $type = self :: record(Item :: class_name(), $parameters);
        if (isset($type[Item :: PROPERTY_TYPE]))
        {
            return $type[Item :: PROPERTY_TYPE];
        }
        else
        {
            throw new ObjectNotExistException($id);
        }
    }

    private static function fetch_item($parameters, $type)
    {
        if ($type :: is_extended())
        {
            $join = new Join(
                Item :: class_name(), 
                new EqualityCondition(
                    new PropertyConditionVariable(Item :: class_name(), Item :: PROPERTY_ID), 
                    new PropertyConditionVariable($type, $type :: PROPERTY_ID)));
            if ($parameters->get_joins() instanceof Joins)
            {
                $joins = $parameters->get_joins();
                $joins->add($join);
                $parameters->set_joins($joins);
            }
            else
            {
                $joins = new Joins(array($join));
                $parameters->set_joins($joins);
            }
        }
        
        return self :: retrieve($type, $parameters);
    }
}