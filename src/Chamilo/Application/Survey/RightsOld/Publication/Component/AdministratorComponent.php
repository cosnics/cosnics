<?php
namespace Chamilo\Application\Survey\Rights\Publication\Component;

use Chamilo\Application\Survey\Rights\Publication\Form\RightsForm;
use Chamilo\Application\Survey\Rights\Publication\Manager;
use Chamilo\Application\Survey\Rights\Rights;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class AdministratorComponent extends Manager
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
                Translation :: get($success ? 'AdministratorRightsSaved' : 'AdministratorRightsNotSaved'), 
                ($success ? false : true));
        }
        else
        {
            
            $html = array();
            
            $html[] = $this->render_header();
            $html[] = $this->get_tabs(self :: ACTION_ADMINISTRATOR, $form->toHtml())->render();
            $html[] = $this->render_footer();
            
            return implode(PHP_EOL, $html);
        }
    }
}
