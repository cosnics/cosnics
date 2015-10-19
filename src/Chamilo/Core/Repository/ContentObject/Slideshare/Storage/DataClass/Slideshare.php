<?php
namespace Chamilo\Core\Repository\ContentObject\Slideshare\Storage\DataClass;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

/**
 * $Id: slideshare.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.content_object.slideshare
 */
class Slideshare extends ContentObject implements Versionable
{
    const CLASS_NAME = __CLASS__;
    const PROPERTY_EMBED = 'embed';

    public static function get_type_name()
    {
        return ClassnameUtilities :: getInstance()->getClassNameFromNamespace(self :: CLASS_NAME, true);
    }

    public function get_embed()
    {
        $default = $this->get_synchronization_data()->get_external_object()->get_default_properties();
        return $default['embed'];
    }

    public static function is_type_available()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance :: class_name(), Instance :: PROPERTY_TYPE),
            new StaticConditionVariable(\Chamilo\Core\Repository\External\Manager :: get_namespace('slideshare')));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance :: class_name(), Instance :: PROPERTY_ENABLED),
            new StaticConditionVariable(1));
        $condition = new AndCondition($conditions);

        $external_repositories = \Chamilo\Core\Repository\Instance\Storage\DataManager :: retrieves(
            Instance :: class_name(),
            new DataClassRetrievesParameters($condition));

        return $external_repositories->size() == 1;
    }
}
