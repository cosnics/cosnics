<?php
namespace Chamilo\Core\Repository\ContentObject\Soundcloud\Storage\DataClass;

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
 * $Id: soundcloud.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.content_object.soundcloud
 */
class Soundcloud extends ContentObject implements Versionable, Includeable
{
    const SOUNDCLOUD_TRACK_API_URI = 'http://api.soundcloud.com/tracks/%s';
    const SOUNDCLOUD_PLAYER_URI = 'http://player.soundcloud.com/player.swf?url=%s&secret_url=false';

    public static function get_type_name()
    {
        return ClassnameUtilities :: getInstance()->getClassNameFromNamespace(self :: class_name(), true);
    }

    public function get_track_api_uri()
    {
        return sprintf(self :: SOUNDCLOUD_TRACK_API_URI, $this->get_synchronization_data()->get_external_object_id());
    }

    public function get_track_player_uri()
    {
        return sprintf(self :: SOUNDCLOUD_PLAYER_URI, urlencode($this->get_track_api_uri()));
    }

    public static function is_type_available()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance :: class_name(), Instance :: PROPERTY_TYPE),
            new StaticConditionVariable(\Chamilo\Core\Repository\External\Manager :: get_namespace('soundcloud')));
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
