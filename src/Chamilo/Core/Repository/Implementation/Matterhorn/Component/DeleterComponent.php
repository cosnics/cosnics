<?php
namespace Chamilo\Core\Repository\Implementation\Matterhorn\Component;

use Chamilo\Core\Repository\Implementation\Matterhorn\Manager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class DeleterComponent extends Manager
{

    public function delete_external_repository_object($id)
    {
        $success = parent::delete_external_repository_object($id);
        
        if ($success)
        {
            $parameters = $this->get_parameters();
            $parameters[self::PARAM_ACTION] = self::ACTION_BROWSE_EXTERNAL_REPOSITORY;
            $this->redirect(Translation::get('ObjectDeleted', null, Utilities::COMMON_LIBRARIES), false, $parameters);
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[self::PARAM_ACTION] = self::ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[self::PARAM_EXTERNAL_REPOSITORY_ID] = $id;
            $this->redirect(
                Translation::get('ObjectFailedDeleted', null, Utilities::COMMON_LIBRARIES), 
                true, 
                $parameters);
        }
    }
}
