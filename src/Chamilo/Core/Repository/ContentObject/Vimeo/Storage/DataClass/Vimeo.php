<?php
namespace Chamilo\Core\Repository\ContentObject\Vimeo\Storage\DataClass;

use Chamilo\Core\Repository\External\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Core\Repository\Instance\Storage\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Includeable;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @author Shoira Mukhsinova
 */
class Vimeo extends ContentObject implements Versionable, Includeable
{
    const VIMEO_PLAYER_URI = 'http://vimeo.com/moogaloop.swf?clip_id=%s&amp;server=vimeo.com&amp;
    show_title=1&amp;show_byline=1&amp;show_portrait=1&amp;color=ffffff&amp;fullscreen=1"';

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }

    public function get_video_url()
    {
        $sync_data = $this->get_synchronization_data();

        if(empty($sync_data)) {
            return false;
        }

        return sprintf(self::VIMEO_PLAYER_URI, $sync_data->get_external_object_id());
    }

    public static function is_type_available()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance::class_name(), Instance::PROPERTY_IMPLEMENTATION),
            new StaticConditionVariable(Manager::get_namespace('Vimeo')));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance::class_name(), Instance::PROPERTY_ENABLED),
            new StaticConditionVariable(1));
        $condition = new AndCondition($conditions);

        $external_repositories = DataManager::retrieves(
            Instance::class_name(),
            new DataClassRetrievesParameters($condition));
        return $external_repositories->size() == 1;
    }
}
