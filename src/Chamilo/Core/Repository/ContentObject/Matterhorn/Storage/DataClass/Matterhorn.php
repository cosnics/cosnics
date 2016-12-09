<?php
namespace Chamilo\Core\Repository\ContentObject\Matterhorn\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Matterhorn\Storage\DataManager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Includeable;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class Matterhorn extends ContentObject implements Versionable, Includeable
{
    const MANAGE_TRACK = 'track';

    private $matterhorn_media_package;

    /**
     *
     * @return the $matterhorn_media_package
     */
    public function get_matterhorn_media_package()
    {
        if (! isset($this->matterhorn_media_package))
        {
            $this->matterhorn_media_package = DataManager::getInstance()->retrieve_media_package_by_object_id(
                $this->get_id());
        }
        return $this->matterhorn_media_package;
    }

    public function set_matterhorn_media_package($matterhorn_media_package)
    {
        $this->matterhorn_media_package = $matterhorn_media_package;
    }

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }

    public function get_video_url()
    {
        $synchronization_data = $this->get_synchronization_data();
        
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Setting::class_name(), Setting::PROPERTY_VARIABLE), 
            new StaticConditionVariable('url'));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Setting::class_name(), Setting::PROPERTY_EXTERNAL_ID), 
            new StaticConditionVariable($synchronization_data->get_external_id()));
        $condition = new AndCondition($conditions);
        $settings = \Chamilo\Core\Repository\Storage\DataManager::retrieve(
            Setting::class_name(), 
            new DataClassRetrieveParameters($condition));
        
        return $settings->get_value() . '/engage/ui/embed.html?id=' . $synchronization_data->get_external_object_id();
    }

    public function create()
    {
        $this->clear_errors();
        
        if ($this->check_before_create())
        {
            return parent::create();
        }
        else
        {
            return false;
        }
    }

    public function update()
    {
        $this->clear_errors();
        
        if ($this->check_before_create())
        {
            return parent::update();
        }
        else
        {
            return false;
        }
    }

    public function check_before_create()
    {
        $synchronization_data = $this->get_synchronization_data();
        
        if (! isset($synchronization_data) || ! $synchronization_data instanceof SynchronizationData)
        {
            $this->add_error(Translation::get('SearchIndexIsRequired'));
        }
        
        return ! $this->has_errors();
    }

    public static function get_managers()
    {
        $managers = array();
        $managers[] = self::MANAGE_TRACK;
        return $managers;
    }

    public static function is_type_available()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance::class_name(), Instance::PROPERTY_IMPLEMENTATION), 
            new StaticConditionVariable(\Chamilo\Core\Repository\External\Manager::get_namespace('matterhorn')));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance::class_name(), Instance::PROPERTY_ENABLED), 
            new StaticConditionVariable(1));
        $condition = new AndCondition($conditions);
        
        return \Chamilo\Core\Repository\Storage\DataManager::count(
            Instance::class_name(), 
            new DataClassCountParameters($condition)) > 0;
    }
}
