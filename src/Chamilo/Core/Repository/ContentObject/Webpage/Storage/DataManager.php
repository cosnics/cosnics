<?php
namespace Chamilo\Core\Repository\ContentObject\Webpage\Storage;

use Chamilo\Core\Repository\ContentObject\Webpage\Storage\DataClass\Webpage;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'repository_';

    public static function get_webpage_id_by_hash($hash)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Webpage::class, Webpage::PROPERTY_HASH),
            new StaticConditionVariable($hash));
        $webpage = \Chamilo\Core\Repository\Storage\DataManager::retrieve_active_content_objects(
            Webpage::class, 
            $condition)->current();
        
        if ($webpage)
        {
            return $webpage->get_id();
        }
        
        return false;
    }

    public static function retrieve_webpage_from_hash($user_id, $hash)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Webpage::class, Webpage::PROPERTY_HASH),
            new StaticConditionVariable($hash));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Webpage::class, Webpage::PROPERTY_OWNER_ID),
            new StaticConditionVariable($user_id));
        
        $condition = new AndCondition($conditions);
        
        return \Chamilo\Core\Repository\Storage\DataManager::retrieve_content_object_by_condition(
            Webpage::class, 
            $condition);
    }

    public static function get_webpage_by_filename($filename)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Webpage::class, Webpage::PROPERTY_FILENAME),
            new StaticConditionVariable($filename));
        return \Chamilo\Core\Repository\Storage\DataManager::retrieve_content_object_by_condition(
            Webpage::class, 
            $condition);
    }

    public static function is_only_webpage_occurence($storage_path, $path)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Webpage::class, Webpage::PROPERTY_STORAGE_PATH),
            new StaticConditionVariable($storage_path));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Webpage::class, Webpage::PROPERTY_PATH),
            new StaticConditionVariable($path));
        $condition = new AndCondition($conditions);
        
        $count = \Chamilo\Core\Repository\Storage\DataManager::count_content_objects(
            Webpage::class, 
            new DataClassCountParameters($condition));
        
        return ($count == 1 ? true : false);
    }
}
