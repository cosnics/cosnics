<?php
namespace Chamilo\Application\CasUser\Component;

use Chamilo\Application\CasUser\Storage\DataClass\AccountRequest;
use Chamilo\Application\CasUser\Storage\DataManager;
use Chamilo\Application\CasUser\Form\AccountRequestForm;
use Chamilo\Application\CasUser\Manager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @author Hans De Bisschop
 */
class EditorComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $account_request = DataManager :: retrieve(
            AccountRequest :: class_name(),
            (int) Request :: get(Manager :: PARAM_REQUEST_ID));
        $form = new AccountRequestForm(
            AccountRequestForm :: TYPE_EDIT,
            $account_request,
            $this->get_url(array(Manager :: PARAM_REQUEST_ID => $account_request->get_id())),
            $this->get_user());

        if ($form->validate())
        {
            $success = $form->update_account_request();
            $this->redirect(
                $success ? Translation :: get('AccountRequestUpdated', null, Utilities :: COMMON_LIBRARIES) : Translation :: get(
                    'AccountRequestNotUpdated',
                    null,
                    Utilities :: COMMON_LIBRARIES),
                ! $success,
                array(Manager :: PARAM_ACTION => Manager :: ACTION_BROWSE));
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode("\n", $html);
        }
    }
}
