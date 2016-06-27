<?php
namespace Chamilo\Core\Repository\ContentObject\Youtube\Storage\DataClass;

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
 * $Id: youtube.class.php
 * 
 * @package repository.lib.content_object.youtube
 */
class Youtube extends ContentObject implements Versionable, Includeable
{
    const YOUTUBE_PLAYER_URI = 'https://www.youtube.com/embed/%s';

    public static function get_type_name()
    {
        return ClassnameUtilities :: getInstance()->getClassNameFromNamespace(self :: class_name(), true);
    }

    public function get_video_url()
    {
        $synchronization_data = $this->get_synchronization_data();
        
        if ($synchronization_data)
        {
            return sprintf(self :: YOUTUBE_PLAYER_URI, $synchronization_data->get_external_object_id());
        }
    }

    public static function is_type_available()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance :: class_name(), Instance :: PROPERTY_IMPLEMENTATION), 
            new StaticConditionVariable('Chamilo\Core\Repository\Implementation\Youtube'));
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
