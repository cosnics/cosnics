<?php
namespace Chamilo\Core\Repository\Common\Action;

use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Platform\Translation;

abstract class ContentObjectActivator extends \Chamilo\Configuration\Package\Action\Activator
{

    public function run()
    {
        $success = parent :: run();
        
        if (! $success)
        {
            return false;
        }
        
        $success = DataManager :: activate_content_object_type(self :: context());
        
        if (! $success)
        {
            $this->failed(Translation :: get('ContentObjectStatusUpdateFailed'));
        }
        else
        {
            return true;
        }
    }
}
