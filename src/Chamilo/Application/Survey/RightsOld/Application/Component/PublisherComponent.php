<?php
namespace Chamilo\Application\Survey\Rights\Application\Component;

use Chamilo\Application\Survey\Rights\Application\Form\RightsForm;
use Chamilo\Application\Survey\Rights\Application\Manager;
use Chamilo\Application\Survey\Rights\Rights;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Translation;

class PublisherComponent extends Manager
{

    public function run()
    {
        
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }
        
        $form = new RightsForm($this, $this->get_url(), Rights :: PUBLISH_RIGHT);
        
        if ($form->validate())
        {
            $success = $form->set_rights();
            
            $this->redirect(
                Translation :: get($success ? 'PublisherRightsSaved' : 'PublisherRightsNotSaved'), 
                ($success ? false : true));
        }
        else
        {
            $html = array();
            
            $html[] = $this->render_header();
            $html[] =  $this->get_tabs(self :: ACTION_PUBLISHER, $form->toHtml())->render();
            $html[] = $this->render_footer();
            return implode(PHP_EOL, $html);
           
        }
    }
}
