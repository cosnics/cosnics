<?php
namespace Chamilo\Core\Repository\Common\Action;

use Chamilo\Configuration\Package\Action\Activator;
use Chamilo\Core\Repository\Storage\DataManager;

class ContentObjectActivator extends Activator
{

    public function run(): bool
    {
        $success = parent::run();

        if (!$success)
        {
            return false;
        }

        $success = DataManager::activate_content_object_type($this->getContext());

        if (!$success)
        {
            return $this->failed(
                $this->getTranslator()->trans('ContentObjectStatusUpdateFailed', [], 'Chamilo\Core\Repository')
            );
        }
        else
        {
            return true;
        }
    }
}
