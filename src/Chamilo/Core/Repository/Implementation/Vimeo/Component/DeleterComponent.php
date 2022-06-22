<?php
namespace Chamilo\Core\Repository\Implementation\Vimeo\Component;

use Chamilo\Core\Repository\Implementation\Vimeo\Manager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class DeleterComponent extends Manager
{

    public function delete_external_repository_object($id)
    {
        $success = parent::delete_external_repository_object($id);
        if ($success)
        {
            $parameters = $this->get_parameters();
            $parameters[Manager::PARAM_ACTION] = Manager::ACTION_BROWSE_EXTERNAL_REPOSITORY;
            $this->redirectWithMessage(Translation::get('ObjectDeleted', null, StringUtilities::LIBRARIES), false, $parameters);
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[Manager::PARAM_ACTION] = Manager::ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[Manager::PARAM_EXTERNAL_REPOSITORY_ID] = $id;
            $this->redirectWithMessage(
                Translation::get('ObjectFailedDeleted', null, StringUtilities::LIBRARIES),
                true, 
                $parameters);
        }
    }
}
