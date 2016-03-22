<?php
namespace Chamilo\Application\CasStorage\Service\Component;

use Chamilo\Application\CasStorage\Service\Form\AccountForm;
use Chamilo\Application\CasStorage\Service\Manager;
use Chamilo\Application\CasStorage\Service\Storage\DataClass\Service;
use Chamilo\Application\CasStorage\Storage\DataManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @author Hans De Bisschop
 */
class UpdaterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $cas_account = DataManager :: retrieve_by_id(
            Service :: class_name(),
            (int) Request :: get(Manager :: PARAM_ACCOUNT_ID));

        $form = new AccountForm(
            AccountForm :: TYPE_EDIT,
            $cas_account,
            $this->get_url(array(self :: PARAM_ACCOUNT_ID => $cas_account->get_id())),
            $this->get_user());

        if ($form->validate())
        {
            $success = $form->update_cas_account();
            $this->redirect(
                $success ? Translation :: get('CasAccountUpdated', null, Utilities :: COMMON_LIBRARIES) : Translation :: get(
                    'CasAccountNotUpdated'),
                ! $success,
                array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
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
