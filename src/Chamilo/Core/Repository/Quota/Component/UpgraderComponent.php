<?php
namespace Chamilo\Core\Repository\Quota\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\Repository\Quota\Manager;
use Chamilo\Libraries\Translation\Translation;

class UpgraderComponent extends Manager
{

    public function run()
    {
        $quota_step = (int) Configuration::getInstance()->get_setting(array('Chamilo\Core\Repository', 'step'));
        
        $calculator = new Calculator($this->get_user());
        
        if ($calculator->upgradeAllowed())
        {
            $user = $this->get_user();
            $user->set_disk_quota($user->get_disk_quota() + $quota_step);
            
            if ($user->update())
            {
                $this->redirect(
                    Translation::get('QuotaUpgraded'), 
                    false, 
                    array(self::PARAM_ACTION => self::ACTION_BROWSE));
            }
        }
        
        $this->redirect(
            Translation::get('QuotaNotUpgraded'), 
            true, 
            array(self::PARAM_ACTION => self::ACTION_BROWSE));
    }
}
