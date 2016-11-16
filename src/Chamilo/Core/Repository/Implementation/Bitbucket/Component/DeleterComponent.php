<?php
namespace Chamilo\Core\Repository\Implementation\Bitbucket\Component;

use Chamilo\Core\Repository\Implementation\Bitbucket\Manager;
use Chamilo\Libraries\Platform\Translation;

class DeleterComponent extends Manager
{

    public function delete_external_repository_object($id)
    {
        $success = parent::delete_external_repository_object($id);
        $message = $success ? Translation::get('RepositoryDeleted') : Translation::get('RepositoryNotDeleted');
        $parameters[Manager::PARAM_ACTION] = Manager::ACTION_BROWSE_EXTERNAL_REPOSITORY;
        
        $this->redirect($message, ! $success, $parameters);
    }
}
