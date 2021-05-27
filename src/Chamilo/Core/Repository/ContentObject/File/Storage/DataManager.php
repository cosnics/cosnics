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
            new PropertyConditionVariable(File::class, File::PROPERTY_HASH), new StaticConditionVariable($hash)
        );
        $file = \Chamilo\Core\Repository\Storage\DataManager::retrieve_active_content_objects(
            File::class, $condition
        )->current();

        if ($file)
        {
            return $file->get_id();
        }

        return false;
    }

    public static function is_only_file_occurence($storage_path, $path)
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(File::class, File::PROPERTY_STORAGE_PATH),
            new StaticConditionVariable($storage_path)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(File::class, File::PROPERTY_PATH), new StaticConditionVariable($path)
        );
        $condition = new AndCondition($conditions);

        $count = \Chamilo\Core\Repository\Storage\DataManager::count_content_objects(
            File::class, new DataClassCountParameters($condition)
        );

        return ($count == 1 ? true : false);
    }
}
