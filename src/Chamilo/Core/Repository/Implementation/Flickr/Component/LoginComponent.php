<?php
namespace Chamilo\Core\Repository\Implementation\Flickr\Component;

use Chamilo\Core\Repository\Implementation\Flickr\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class LoginComponent extends Manager
{

    public function run()
    {
        if (! Setting::get('session_token', $this->get_external_repository()->get_id()))
        {
            if ($this->get_external_repository_manager_connector()->login())
            {
                if (! \Chamilo\Core\Repository\Instance\Storage\DataManager::activate_instance_objects(
                    $this->get_external_repository()->get_id(), 
                    $this->get_user_id(), 
                    $this->get_external_repository_manager_connector()->retrieve_user_id()))
                {
                    $parameters = $this->get_parameters();
                    $parameters[Manager::PARAM_ACTION] = Manager::ACTION_BROWSE_EXTERNAL_REPOSITORY;
                    $this->redirect(
                        Translation::get('LoginFailed', null, Utilities::COMMON_LIBRARIES), 
                        true, 
                        $parameters);
                }
                
                $parameters = $this->get_parameters();
                $parameters[Manager::PARAM_ACTION] = Manager::ACTION_BROWSE_EXTERNAL_REPOSITORY;
                
                $this->redirect(
                    Translation::get('LoginSuccessful', null, Utilities::COMMON_LIBRARIES), 
                    false, 
                    $parameters);
            }
            else
            {
                
                $parameters = $this->get_parameters();
                $parameters[Manager::PARAM_ACTION] = Manager::ACTION_BROWSE_EXTERNAL_REPOSITORY;
                $this->redirect(Translation::get('LoginFailed', null, Utilities::COMMON_LIBRARIES), true, $parameters);
            }
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[Manager::PARAM_ACTION] = Manager::ACTION_BROWSE_EXTERNAL_REPOSITORY;
            $this->redirect('', false, $parameters);
        }
    }
}
