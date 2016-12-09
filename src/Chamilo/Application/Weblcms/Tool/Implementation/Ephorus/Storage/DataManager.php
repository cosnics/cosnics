<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class represents the datamanager for this tool
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'weblcms_ephorus_';

    /**
     * Retrieves a request by a given guid
     * 
     * @param string $guid
     *
     * @throws \InvalidArgumentException
     *
     * @return \application\weblcms\tool\ephorus\Request
     */
    public static function retrieve_request_by_guid($guid)
    {
        if (! $guid)
        {
            throw new \InvalidArgumentException('A valid guid is required to retrieve a request by guid');
        }
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Request::class_name(), Request::PROPERTY_GUID), 
            new StaticConditionVariable($guid));
        
        return static::retrieve(Request::class_name(), new DataClassRetrieveParameters($condition));
    }

    /**
     * Retrieves a request by a given id
     * 
     * @param string $id
     *
     * @throws \InvalidArgumentException
     *
     * @return \application\weblcms\tool\ephorus\Request
     */
    public static function retrieve_request_by_id($id)
    {
        if (! $id)
        {
            throw new \InvalidArgumentException('A valid id is required to retrieve a request by id');
        }
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Request::class_name(), Request::PROPERTY_ID), 
            new StaticConditionVariable($id));
        
        return static::retrieve(Request::class_name(), new DataClassRetrieveParameters($condition));
    }
}
