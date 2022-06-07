<?php
namespace Chamilo\Core\Repository\Implementation\GoogleDocs\Component;

use Chamilo\Core\Repository\Implementation\GoogleDocs\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Core\Repository\Instance\Storage\DataManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class LoginComponent extends Manager
{

    public function run()
    {
        $setting = DataManager::retrieveUserSetting(
            $this->get_external_repository()->getId(), 
            $this->getUser()->getId(), 
            'session_token');
        
        if (! $setting instanceof Setting)
        {
            if ($this->get_external_repository_manager_connector()->login())
            {
                $parameters = $this->get_parameters();
                $parameters[self::PARAM_ACTION] = self::ACTION_BROWSE_EXTERNAL_REPOSITORY;
                
                $this->redirect(
                    Translation::get('LoginSuccessful', null, StringUtilities::LIBRARIES),
                    false, 
                    $parameters);
            }
            else
            {
                $parameters = $this->get_parameters();
                $parameters[self::PARAM_ACTION] = self::ACTION_BROWSE_EXTERNAL_REPOSITORY;
                $this->redirect(Translation::get('LoginFailed', null, StringUtilities::LIBRARIES), true, $parameters);
            }
        }
        else
        
        {
            $parameters = $this->get_parameters();
            $parameters[self::PARAM_ACTION] = self::ACTION_BROWSE_EXTERNAL_REPOSITORY;
            $this->redirect(Translation::get('LoginSuccessful', null, StringUtilities::LIBRARIES), false, $parameters);
        }
    }
}
