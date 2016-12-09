<?php
namespace Chamilo\Application\CasStorage\Component;

use Chamilo\Application\CasStorage\Form\AccountRequestForm;
use Chamilo\Application\CasStorage\Manager;
use Chamilo\Application\CasStorage\Storage\DataClass\AccountRequest;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class CreatorComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $account_request = new AccountRequest();
        $form = new AccountRequestForm(
            AccountRequestForm::TYPE_CREATE, 
            $account_request, 
            $this->get_url(), 
            $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_account_request();
            if ($success)
            {
                $this->redirect(
                    Translation::get('AccountRequestCreated', null, Utilities::COMMON_LIBRARIES), 
                    false, 
                    array(
                        Application::PARAM_ACTION => Manager::ACTION_BROWSE, 
                        Manager::PARAM_REQUEST_ID => $account_request->get_id()));
            }
            else
            {
                $this->redirect(
                    Translation::get('AccountRequestNotCreated', null, Utilities::COMMON_LIBRARIES), 
                    true, 
                    array(Application::PARAM_ACTION => Manager::ACTION_BROWSE));
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
