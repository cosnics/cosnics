<?php
namespace Chamilo\Core\Repository\Implementation\Flickr\Component;

use Chamilo\Core\Repository\Implementation\Flickr\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Core\Repository\Instance\Storage\DataManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class LoginComponent extends Manager
{

    public function run()
    {
        if (! Setting::get('session_token', $this->get_external_repository()->get_id()))
        {
            if ($this->get_external_repository_manager_connector()->login())
            {
                if (! DataManager::activate_instance_objects(
                    $this->get_external_repository()->get_id(), 
                    $this->get_user_id(), 
                    $this->get_external_repository_manager_connector()->retrieve_user_id()))
                {
                    $parameters = $this->get_parameters();
                    $parameters[Manager::PARAM_ACTION] = Manager::ACTION_BROWSE_EXTERNAL_REPOSITORY;
                    $this->redirectWithMessage(
                        Translation::get('LoginFailed', null, StringUtilities::LIBRARIES),
                        true, 
                        $parameters);
                }
                
                $parameters = $this->get_parameters();
                $parameters[Manager::PARAM_ACTION] = Manager::ACTION_BROWSE_EXTERNAL_REPOSITORY;
                
                $this->redirectWithMessage(
                    Translation::get('LoginSuccessful', null, StringUtilities::LIBRARIES),
                    false, 
                    $parameters);
            }
            else
            {
                
                $parameters = $this->get_parameters();
                $parameters[Manager::PARAM_ACTION] = Manager::ACTION_BROWSE_EXTERNAL_REPOSITORY;
                $this->redirectWithMessage(Translation::get('LoginFailed', null, StringUtilities::LIBRARIES), true, $parameters);
            }
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[Manager::PARAM_ACTION] = Manager::ACTION_BROWSE_EXTERNAL_REPOSITORY;
            $this->redirectWithMessage('', false, $parameters);
        }
    }
}
