<?php
namespace Chamilo\Core\Repository\Implementation\GoogleDocs\Component;

use Chamilo\Core\Repository\Implementation\GoogleDocs\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

class LogoutComponent extends Manager
{

    public function run()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Setting :: class_name(), Setting :: PROPERTY_EXTERNAL_ID), 
            new StaticConditionVariable($this->get_external_repository()->get_id()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Setting :: class_name(), Setting :: PROPERTY_VARIABLE), 
            new StaticConditionVariable('session_token'));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Setting :: class_name(), Setting :: PROPERTY_USER_SETTING), 
            new StaticConditionVariable(1));
        $condition = new AndCondition($conditions);
        
        $parameters = new DataClassRetrievesParameters($condition, 1);
        $settings = \Chamilo\Core\Repository\Storage\DataManager :: retrieves(Setting :: class_name(), $parameters);
        
        if ($settings->size() > 0)
        {
            $setting = $settings->next_result();
            
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Setting :: class_name(), Setting :: PROPERTY_SETTING_ID), 
                new StaticConditionVariable($setting->get_id()));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Setting :: class_name(), Setting :: PROPERTY_USER_ID), 
                new StaticConditionVariable($this->get_user_id()));
            $condition = new AndCondition($conditions);
            
            $parameters = new DataClassRetrievesParameters($condition, 1);
            $user_settings = \Chamilo\Core\Repository\Storage\DataManager :: retrieves(
                Setting :: class_name(), 
                $parameters);
            
            if ($user_settings->size() > 0)
            {
                $user_setting = $user_settings->next_result();
                if ($user_setting->delete())
                {
                    $parameters = $this->get_parameters();
                    $parameters[self :: PARAM_ACTION] = self :: ACTION_BROWSE_EXTERNAL_REPOSITORY;
                    $this->redirect(
                        Translation :: get('LogoutSuccessful', null, Utilities :: COMMON_LIBRARIES), 
                        false, 
                        $parameters);
                }
                else
                {
                    $parameters = $this->get_parameters();
                    $parameters[self :: PARAM_ACTION] = self :: ACTION_BROWSE_EXTERNAL_REPOSITORY;
                    $this->redirect(
                        Translation :: get('LogoutFailed', null, Utilities :: COMMON_LIBRARIES), 
                        true, 
                        $parameters);
                }
            }
        }
    }
}
