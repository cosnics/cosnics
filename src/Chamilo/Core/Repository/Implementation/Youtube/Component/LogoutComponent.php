<?php
namespace Chamilo\Core\Repository\Implementation\Youtube\Component;

use Chamilo\Core\Repository\Implementation\Youtube\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Translation\Translation;
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
            new PropertyConditionVariable(Setting::class, Setting::PROPERTY_EXTERNAL_ID),
            new StaticConditionVariable($this->get_external_repository()->get_id()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Setting::class, Setting::PROPERTY_VARIABLE),
            new StaticConditionVariable('session_token'));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Setting::class, Setting::PROPERTY_USER_ID),
            new StaticConditionVariable($this->getUser()->getId()));
        $condition = new AndCondition($conditions);
        
        $parameters = new DataClassRetrievesParameters($condition, 1);
        $settings = DataManager::retrieves(Setting::class, $parameters);
        
        if ($settings->count() > 0)
        {
            $user_setting = $settings->current();
            if ($user_setting->delete())
            {
                $parameters = $this->get_parameters();
                $parameters[Manager::PARAM_ACTION] = Manager::ACTION_BROWSE_EXTERNAL_REPOSITORY;
                $this->redirect(
                    Translation::get('LogoutSuccessful', null, Utilities::COMMON_LIBRARIES), 
                    false, 
                    $parameters);
            }
            else
            {
                $parameters = $this->get_parameters();
                $parameters[Manager::PARAM_ACTION] = Manager::ACTION_BROWSE_EXTERNAL_REPOSITORY;
                $this->redirect(Translation::get('LogoutFailed', null, Utilities::COMMON_LIBRARIES), true, $parameters);
            }
        }
    }
}
