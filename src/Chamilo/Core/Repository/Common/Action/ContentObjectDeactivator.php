<?php
namespace Chamilo\Core\Repository\Common\Action;

use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Translation\Translation;

abstract class ContentObjectDeactivator extends \Chamilo\Configuration\Package\Action\Deactivator
{

    public function run()
    {
        $success = parent::run();
        
        if (! $success)
        {
            return false;
        }
        
        $success = DataManager::deactivate_content_object_type(self::context());
        
        if (! $success)
        {
            $this->failed(Translation::get('ContentObjectStatusUpdateFailed'));
        }
        else
        {
            return true;
        }
    }
}
