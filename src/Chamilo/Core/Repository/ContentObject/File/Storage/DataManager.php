<?php
namespace Chamilo\Core\Repository\ContentObject\File\Storage;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'repository_';

    public static function get_file_id_by_hash($hash)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(File :: class_name(), File :: PROPERTY_HASH),
            new StaticConditionVariable($hash));
        $file = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_active_content_objects(
            File :: class_name(),
            $condition)->next_result();

        if ($file)
        {
            return $file->get_id();
        }

        return false;
    }

    public static function retrieve_file_from_hash($user_id, $hash)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(File :: class_name(), File :: PROPERTY_HASH),
            new StaticConditionVariable($hash),
            'file');
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(File :: class_name(), File :: PROPERTY_OWNER_ID),
            new StaticConditionVariable($user_id));

        $condition = new AndCondition($conditions);

        return \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object_by_condition(
            File :: class_name(),
            $condition);
    }

    public static function get_file_by_filename($filename)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(File :: class_name(), File :: PROPERTY_FILENAME),
            new StaticConditionVariable($filename),
            'file');
        return \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object_by_condition(
            File :: class_name(),
            $condition);
    }

    public static function is_only_file_occurence($storage_path, $path)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(File :: class_name(), File :: PROPERTY_STORAGE_PATH),
            new StaticConditionVariable($storage_path));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(File :: class_name(), File :: PROPERTY_PATH),
            new StaticConditionVariable($path));
        $condition = new AndCondition($conditions);

        $count = \Chamilo\Core\Repository\Storage\DataManager :: count_content_objects(
            File :: class_name(),
            new DataClassCountParameters($condition));

        return ($count == 1 ? true : false);
    }
}
