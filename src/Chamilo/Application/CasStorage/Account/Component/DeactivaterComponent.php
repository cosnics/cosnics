<?php
namespace Chamilo\Application\CasStorage\Account\Component;

/**
 *
 * @author Hans De Bisschop
 */
use Chamilo\Application\CasStorage\Account\Manager;
use Chamilo\Application\CasStorage\Account\Storage\DataClass\Account;
use Chamilo\Application\CasStorage\Account\Storage\DataManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class DeactivaterComponent extends Manager
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
                $cas_account->set_status(Account :: STATUS_DISABLED);
                if (! $cas_account->update())
                {
                    $failures ++;
                }
            }

            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedCasAccountNotDeactivated';
                }
                else
                {
                    $message = 'SelectedCasAccountsNotDeactivated';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedCasAccountDeactivated';
                }
                else
                {
                    $message = 'SelectedCasAccountsDeactivated';
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
                htmlentities(Translation :: get('NoCasAccountSelected', null, Utilities :: COMMON_LIBRARIES)));
        }
    }
}
