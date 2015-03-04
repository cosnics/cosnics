<?php
namespace Chamilo\Application\Survey\Component;

use Chamilo\Application\Survey\Form\PublicationForm;
use Chamilo\Application\Survey\Manager;
use Chamilo\Application\Survey\Rights\Rights;
use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Application\Survey\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

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
        
        if (! Rights :: is_allowed_in_surveys_subtree(
            Rights :: RIGHT_EDIT, 
            $publication->get_id(), 
            Rights :: TYPE_PUBLICATION))
        
        {
           throw new NotAllowedException();
        }
        
        $form = new PublicationForm(
            PublicationForm :: TYPE_EDIT, 
            $publication, 
            $this->get_user(), 
            $this->get_url(array(self :: PARAM_PUBLICATION_ID => $publication->get_id())), 
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
            $this->display_header();
            $form->display();
            $this->display_footer();
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        Manager :: PARAM_ACTION => Manager :: ACTION_BROWSE, 
                        DynamicTabsRenderer :: PARAM_SELECTED_TAB => BrowserComponent :: TAB_MY_PUBLICATIONS)), 
                Translation :: get('BrowserComponent')));
    }

    function get_parameters()
    {
        return array(self :: PARAM_PUBLICATION_ID);
    }
}
?>