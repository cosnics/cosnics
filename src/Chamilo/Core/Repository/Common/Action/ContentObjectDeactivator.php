<?php
namespace Chamilo\Core\Repository\Common\Action;

use Chamilo\Configuration\Package\Action\Deactivator;
use Chamilo\Core\Repository\Storage\DataManager;

class ContentObjectDeactivator extends Deactivator
{

    public function run(): bool
    {
        $success = parent::run();

        if (!$success)
        {
            return false;
        }

        $success = DataManager::deactivate_content_object_type($this->getContext());

        if (!$success)
        {
            return $this->failed($this->getTranslator()->trans('ContentObjectStatusUpdateFailed'));
        }
        else
        {
            return true;
        }
    }
}
