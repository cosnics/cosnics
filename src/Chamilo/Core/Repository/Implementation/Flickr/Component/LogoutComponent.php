<?php
namespace Chamilo\Core\Repository\Implementation\Flickr\Component;

use Chamilo\Core\Repository\Implementation\Flickr\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

class LogoutComponent extends Manager
{

    public function run()
    {
        $external_user_id = $this->get_external_repository_manager_connector()->retrieve_user_id();
        if (! $this->get_external_repository_manager_connector()->logout())
        {
            $this->failed();
        }
        
        if (! \Chamilo\Core\Repository\Instance\Storage\DataManager::deactivate_instance_objects(
            $this->get_external_repository()->get_id(), 
            $this->get_user_id(), 
            $external_user_id))
        {
            $this->failed();
        }
        
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Setting::class_name(), Setting::PROPERTY_EXTERNAL_ID), 
            new StaticConditionVariable($this->get_external_repository()->get_id()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Setting::class_name(), Setting::PROPERTY_VARIABLE), 
            new StaticConditionVariable('session_token'));
        
        $condition = new AndCondition($conditions);
        
        $setting = DataManager::retrieve(
            Setting::class_name(), 
            new DataClassRetrieveParameters($condition));
        $setting->set_value(null);
        
        $parameters = $this->get_parameters();
        $parameters[Manager::PARAM_ACTION] = Manager::ACTION_BROWSE_EXTERNAL_REPOSITORY;
        
        $setting->update();
        $this->redirect(Translation::get('LogoutSuccessful', null, Utilities::COMMON_LIBRARIES), false, $parameters);
    }

    public function failed()
    {
        $parameters = $this->get_parameters();
        $parameters[Manager::PARAM_ACTION] = Manager::ACTION_BROWSE_EXTERNAL_REPOSITORY;
        $this->redirect(Translation::get('LogoutFailed', null, Utilities::COMMON_LIBRARIES), true, $parameters);
    }
}
