<?php
namespace Chamilo\Application\Survey\Component;

use Chamilo\Application\Survey\Form\PublicationForm;
use Chamilo\Application\Survey\Manager;
use Chamilo\Application\Survey\Rights\Rights;
use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Application\Survey\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class EditorComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $publication = DataManager :: retrieve_by_id(
            Publication :: class_name(), 
            Request :: get(self :: PARAM_PUBLICATION_ID));
        
        if (! Rights :: getInstance()->is_right_granted(
            Rights :: RIGHT_EDIT, 
            $publication->getId()))
        
        {
           throw new NotAllowedException();
        }
        
        $form = new PublicationForm(
            PublicationForm :: TYPE_EDIT, 
            $publication, 
            $this->get_user(), 
            $this->get_url(array(self :: PARAM_PUBLICATION_ID => $publication->getId())), 
            $publication);
        
        if ($form->validate())
        {
            $success = $form->update_publication();
            $this->redirect(
                $success ? Translation :: get('PublicationUpdated') : Translation :: get('PublicationNotUpdated'), 
                ! $success, 
                array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
        }
        else
        {
            $html = array();
            
            $html[] = $this->render_header();
            $html[] =$form->toHtml();
            $html[] = $this->render_footer();
            
            return implode(PHP_EOL, $html);
          
        }
    }

}
?>