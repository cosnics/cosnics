<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\StorageParameters;
use InvalidArgumentException;

/**
 * This class represents the datamanager for this tool
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DataManager extends \Chamilo\Libraries\Storage\Repository\DataManager
{
    public const PREFIX = 'weblcms_ephorus_';

    /**
     * Retrieves a request by a given guid
     *
     * @param string $guid
     *
     * @return \application\weblcms\tool\ephorus\Request
     * @throws \InvalidArgumentException
     *
     */
    public static function retrieve_request_by_guid($guid)
    {
        if (!$guid)
        {
            throw new InvalidArgumentException('A valid guid is required to retrieve a request by guid');
        }

        $condition = new EqualityCondition(
            new PropertyConditionVariable(Request::class, Request::PROPERTY_GUID), new StaticConditionVariable($guid)
        );

        return static::retrieve(Request::class, new StorageParameters(condition: $condition));
    }
}
