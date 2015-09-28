<?php
namespace Chamilo\Application\CasStorage\Service\Component;

/**
 *
 * @author Hans De Bisschop
 */
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Application\CasStorage\Account\Storage\DataClass\Account;
use Chamilo\Application\CasStorage\Service\Manager;
use Chamilo\Application\CasStorage\Service\Storage\DataManager;

class ActivaterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $ids = Request :: get(self :: PARAM_ACCOUNT_ID);
        $failures = 0;

        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            foreach ($ids as $id)
            {
                $cas_account = DataManager :: retrieve_by_id(Account :: class_name(), (int) $id);
                $cas_account->set_status(Account :: STATUS_ENABLED);
                if (! $cas_account->update())
                {
                    $failures ++;
                }
            }

            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedCasAccountNotActivated';
                }
                else
                {
                    $message = 'SelectedCasAccountsNotActivated';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedCasAccountActivated';
                }
                else
                {
                    $message = 'SelectedCasAccountsActivated';
                }
            }
            $this->redirect(
                Translation :: get($message, null, Utilities :: COMMON_LIBRARIES),
                ($failures ? true : false),
                array(self :: PARAM_CAS_ACCOUNT_ACTION => self :: ACTION_BROWSE));
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation :: get(
                        'NoCasAccountSelected',
                        Translation :: get($message, null, Utilities :: COMMON_LIBRARIES))));
        }
    }
}
