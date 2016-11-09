<?php
namespace Chamilo\Core\Admin\Storage;

use Chamilo\Core\Admin\Storage\DataClass\RemotePackage;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package admin.lib
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'admin_';

    /**
     * Retrieves a remote package with given context
     * 
     * @param $context string
     * @return RemotePackage
     */
    public static function retrieve_remote_package_by_context($context)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(RemotePackage :: class_name(), RemotePackage :: PROPERTY_CONTEXT), 
            new StaticConditionVariable($context));
        return self :: getInstance()->retrieve_remote_packages($condition)->next_result();
    }
}
