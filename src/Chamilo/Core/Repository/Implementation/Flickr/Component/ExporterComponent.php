<?php
namespace Chamilo\Core\Repository\Implementation\Flickr\Component;

use Chamilo\Core\Repository\Implementation\Flickr\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class ExporterComponent extends Manager implements DelegateComponent
{

    public function export_external_repository_object($object)
    {
        $success = parent::export_external_repository_object($object);
        if ($success)
        {
            $parameters = $this->get_parameters();
            $parameters[Manager::PARAM_ACTION] = Manager::ACTION_BROWSE_EXTERNAL_REPOSITORY;
            $this->redirectWithMessage(Translation::get('ObjectExported', null, StringUtilities::LIBRARIES), false, $parameters);
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[Manager::PARAM_ACTION] = Manager::ACTION_EXPORT_EXTERNAL_REPOSITORY;
            $this->redirectWithMessage(
                Translation::get('ObjectFailedExported', null, StringUtilities::LIBRARIES),
                true, 
                $parameters);
        }
    }
}
