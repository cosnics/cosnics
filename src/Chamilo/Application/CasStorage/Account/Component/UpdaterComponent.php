<?php
namespace Chamilo\Application\CasStorage\Account\Component;

use Chamilo\Application\CasStorage\Account\Form\AccountForm;
use Chamilo\Application\CasStorage\Account\Manager;
use Chamilo\Application\CasStorage\Account\Storage\DataClass\Account;
use Chamilo\Application\CasStorage\Account\Storage\DataManager;
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
        $cas_account = DataManager::retrieve_by_id(
            Account::class_name(), 
            (int) Request::get(Manager::PARAM_ACCOUNT_ID));
        
        $form = new AccountForm(
            AccountForm::TYPE_EDIT, 
            $cas_account, 
            $this->get_url(array(self::PARAM_ACCOUNT_ID => $cas_account->get_id())), 
            $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->update_cas_account();
            $this->redirect(
                $success ? Translation::get('CasAccountUpdated', null, Utilities::COMMON_LIBRARIES) : Translation::get(
                    'CasAccountNotUpdated'), 
                ! $success, 
                array(self::PARAM_ACTION => self::ACTION_BROWSE));
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
