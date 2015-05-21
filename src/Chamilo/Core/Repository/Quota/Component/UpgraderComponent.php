<?php
namespace Chamilo\Core\Repository\Quota\Component;

use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\Repository\Quota\Manager;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;

class UpgraderComponent extends Manager
{

    public function run()
    {
        $quota_step = (int) PlatformSetting :: get('step', __NAMESPACE__);
        
        $calculator = new Calculator($this->get_user());
        
        if ($calculator->upgrade_allowed())
        {
            $user = $this->get_user();
            $user->set_disk_quota($user->get_disk_quota() + $quota_step);
            
            if ($user->update())
            {
                $this->redirect(
                    Translation :: get('QuotaUpgraded'), 
                    false, 
                    array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
            }
        }
        
        $this->redirect(
            Translation :: get('QuotaNotUpgraded'), 
            true, 
            array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
    }
}
