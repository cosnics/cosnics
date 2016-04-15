<?php
namespace Chamilo\Application\CasStorage\Service\Component;

use Chamilo\Application\CasStorage\Service\Manager;
use Chamilo\Application\CasStorage\Service\Storage\DataClass\Service;
use Chamilo\Application\CasStorage\Service\Storage\DataManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @author Hans De Bisschop
 */
class DeleterComponent extends Manager
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
                $cas_account = DataManager :: retrieve_by_id(Service :: class_name(), (int) $id);

                if (! $cas_account->delete())
                {
                    $failures ++;
                }
            }

            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedCasAccountNotDeleted';
                }
                else
                {
                    $message = 'SelectedCasAccountsNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedCasAccountDeleted';
                }
                else
                {
                    $message = 'SelectedCasAccountsDeleted';
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
