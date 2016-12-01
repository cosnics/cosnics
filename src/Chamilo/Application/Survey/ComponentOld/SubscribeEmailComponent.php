<?php
namespace Chamilo\Application\Survey\Component;

use Chamilo\Application\Survey\Form\SubscribeMailForm;
use Chamilo\Application\Survey\Manager;
use Chamilo\Application\Survey\Rights\Rights;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class SubscribeEmailComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $publication_id = Request::get(self::PARAM_PUBLICATION_ID);
        
        if (! Rights::getInstance()->is_right_granted(Rights::INVITE_RIGHT, $publication_id))
        {
            $this->not_allowed(false);
        }
        
        $form = new SubscribeMailForm(
            $publication_id, 
            $this->get_url(array(self::PARAM_PUBLICATION_ID => Request::get(self::PARAM_PUBLICATION_ID))));
        
        if ($form->validate())
        {
            $no_user_emails = $form->create_email_rights();
            if (count($no_user_emails) == 0)
            {
                $this->redirect(
                    Translation::get('SurveyUsersSubscribed'), 
                    (false), 
                    array(
                        self::PARAM_ACTION => self::ACTION_BROWSE_PARTICIPANTS, 
                        self::PARAM_PUBLICATION_ID => $publication_id, 
                        DynamicTabsRenderer::PARAM_SELECTED_TAB => ParticipantBrowserComponent::TAB_INVITEES));
            }
            else
            {
                $emails = implode(', ', $no_user_emails);
                $this->redirect(
                    Translation::get('SurveyUsersNotSubscribed: ' . $emails), 
                    (true), 
                    array(
                        self::PARAM_ACTION => self::ACTION_BROWSE_PARTICIPANTS, 
                        self::PARAM_PUBLICATION_ID => $publication_id, 
                        DynamicTabsRenderer::PARAM_SELECTED_TAB => ParticipantBrowserComponent::TAB_INVITEES));
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
?>