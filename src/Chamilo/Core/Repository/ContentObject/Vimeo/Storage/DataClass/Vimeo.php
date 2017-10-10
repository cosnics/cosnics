<?php
namespace Chamilo\Core\Repository\ContentObject\Vimeo\Storage\DataClass;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
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
        $video_url_custom = sprintf(self::VIMEO_PLAYER_URI, $this->get_synchronization_data()->get_external_object_id());

        return $video_url_custom;
    }

    public static function is_type_available()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance::class_name(), Instance::PROPERTY_IMPLEMENTATION),
            new StaticConditionVariable(\Chamilo\Core\Repository\External\Manager::get_namespace('Vimeo')));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance::class_name(), Instance::PROPERTY_ENABLED),
            new StaticConditionVariable(1));
        $condition = new AndCondition($conditions);

        $external_repositories = \Chamilo\Core\Repository\Instance\Storage\DataManager::retrieves(
            Instance::class_name(),
            new DataClassRetrievesParameters($condition));
        return $external_repositories->size() == 1;
    }
}
