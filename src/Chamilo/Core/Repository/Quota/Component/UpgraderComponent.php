<?php
namespace Chamilo\Core\Repository\Quota\Component;

use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\Repository\Quota\Manager;
use Chamilo\Libraries\Translation\Translation;

class UpgraderComponent extends Manager
{

    public function run()
    {
        $quota_step = (int) $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Repository', 'step']);

        $calculator = new Calculator($this->get_user());

        if ($calculator->upgradeAllowed())
        {
            $user = $this->get_user();
            $user->set_disk_quota($user->get_disk_quota() + $quota_step);

            if ($user->update())
            {
                $this->redirectWithMessage(
                    Translation::get('QuotaUpgraded'), false, [self::PARAM_ACTION => self::ACTION_BROWSE]
                );
            }
        }

        $this->redirectWithMessage(
            Translation::get('QuotaNotUpgraded'), true, [self::PARAM_ACTION => self::ACTION_BROWSE]
        );
    }
}
