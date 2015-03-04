<?php
namespace Chamilo\Application\Survey\Rights\Publication\Component;

use Chamilo\Application\Survey\Rights\Publication\Form\RightsForm;
use Chamilo\Application\Survey\Rights\Publication\Manager;
use Chamilo\Application\Survey\Rights\Rights;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class MailerComponent extends Manager
{

    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }
        
        $publication_id = Request :: get(\Chamilo\Application\Survey\Manager :: PARAM_PUBLICATION_ID);
        
        $form = new RightsForm($this, $this->get_url(), Rights :: MAIL_RIGHT, $publication_id);
        
        if ($form->validate())
        {
            $success = $form->set_rights();
            
            $this->redirect(
                Translation :: get($success ? 'MailerRightsSaved' : 'MailerRightsNotSaved'), 
                ($success ? false : true));
        }
        else
        {
            $this->display_header();
            echo $this->get_tabs(self :: ACTION_MAILER, $form->toHtml())->render();
            $this->display_footer();
        }
    }
}
