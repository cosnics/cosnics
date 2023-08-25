<?php
namespace Chamilo\Core\Repository\Common\Action;

use Chamilo\Configuration\Package\Action\Deactivator;
use Chamilo\Core\Repository\Storage\DataManager;

abstract class ContentObjectDeactivator extends Deactivator
{

    public function run(): bool
    {
        $success = parent::run();

        if (!$success)
        {
            return false;
        }

        $success = DataManager::deactivate_content_object_type(static::CONTEXT);

        if (!$success)
        {
            $this->failed($this->getTranslator()->trans('ContentObjectStatusUpdateFailed'));
        }
        else
        {
            return true;
        }
    }
}
