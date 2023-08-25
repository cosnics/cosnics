<?php
namespace Chamilo\Core\Repository\Common\Action;

use Chamilo\Configuration\Package\Action\Activator;
use Chamilo\Core\Repository\Storage\DataManager;

abstract class ContentObjectActivator extends Activator
{

    public function run(): bool
    {
        $success = parent::run();

        if (!$success)
        {
            return false;
        }

        $success = DataManager::activate_content_object_type(static::CONTEXT);

        if (!$success)
        {
            $this->failed(
                $this->getTranslator()->trans('ContentObjectStatusUpdateFailed', [], 'Chamilo\Core\Repository')
            );
        }
        else
        {
            return true;
        }
    }
}
