<?php
namespace Chamilo\Application\CasStorage\Service\Component;

use Chamilo\Application\CasStorage\Service\Form\AccountForm;
use Chamilo\Application\CasStorage\Service\Manager;
use Chamilo\Application\CasStorage\Service\Storage\DataClass\Service;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class CreatorComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $cas_account = new Service();
        $form = new AccountForm(AccountForm :: TYPE_CREATE, $cas_account, $this->get_url(), $this->get_user());

        if ($form->validate())
        {
            $success = $form->create_cas_account();
            if ($success)
            {
                $this->redirect(
                    Translation :: get('CasAccountCreated', null, Utilities :: COMMON_LIBRARIES),
                    (false),
                    array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
            }
            else
            {
                $this->redirect(
                    Translation :: get('CasAccountNotCreated', null, Utilities :: COMMON_LIBRARIES),
                    (true),
                    array(self :: PARAM_CAS_ACCOUNT_ACTION => self :: ACTION_BROWSE));
            }
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }
}
