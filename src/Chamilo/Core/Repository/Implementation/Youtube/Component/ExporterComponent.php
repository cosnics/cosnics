<?php
namespace Chamilo\Core\Repository\Implementation\Youtube\Component;

use Chamilo\Core\Repository\Implementation\Youtube\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class ExporterComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        if (! $this->get_external_repository()->get_user_setting(Session::get_user_id(), 'session_token'))
        {
            throw new NotAllowedException();
        }
        else
        {
            return parent::run();
        }
    }

    public function export_external_repository_object($object)
    {
        $success = parent::export_external_repository_object($object);
        if ($success)
        {
            $parameters = $this->get_parameters();
            $parameters[Manager::PARAM_ACTION] = Manager::ACTION_BROWSE_EXTERNAL_REPOSITORY;
            $parameters[Manager::PARAM_FEED_TYPE] = Manager::FEED_TYPE_MYVIDEOS;
            $this->redirect(Translation::get('ObjectExported', null, Utilities::COMMON_LIBRARIES), false, $parameters);
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[Manager::PARAM_ACTION] = Manager::ACTION_EXPORT_EXTERNAL_REPOSITORY;
            $this->redirect(
                Translation::get('ObjectFailedExported', null, Utilities::COMMON_LIBRARIES), 
                true, 
                $parameters);
        }
    }
}
