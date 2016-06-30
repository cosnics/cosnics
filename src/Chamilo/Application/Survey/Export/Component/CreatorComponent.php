<?php
namespace Chamilo\Application\Survey\Export\Component;

use Chamilo\Application\Survey\Export\Form\ExportTemplateForm;
use Chamilo\Application\Survey\Export\Manager;
use Chamilo\Application\Survey\Export\Storage\DataClass\ExportTemplate;
use Chamilo\Application\Survey\Service\RightsService;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class CreatorComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $publication_id = Request :: get(Manager :: PARAM_PUBLICATION_ID);
        $id = Request :: get(self :: PARAM_EXPORT_REGISTRATION_ID);
        
        if (RightsService :: getInstance())
        {
            
            $export_template = new ExportTemplate();
            $export_template->set_publication_id($publication_id);
            $export_template->set_export_registration_id($id);
            $export_template->set_owner_id($this->get_user_id());
            
            $form = new ExportTemplateForm(ExportTemplateForm :: TYPE_CREATE, $this->get_url(), $export_template);
            
            if ($form->validate())
            {
                $success = $form->create();
                
                if ($success)
                {
                    $message = 'SelectedExportRegistrationActivated';
                    $tab = BrowserComponent :: TAB_EXPORT_TEMPLATES;
                }
                else
                {
                    $message = 'SelectedExportRegistrationNotActivated';
                    $tab = BrowserComponent :: TAB_EXPORT_REGISTRATIONS;
                }
                
                $this->redirect(
                    Translation :: get($message), 
                    ! $success, 
                    array(
                        self :: PARAM_ACTION => self :: ACTION_BROWSE, 
                        Manager :: PARAM_PUBLICATION_ID => $publication_id, 
                        DynamicTabsRenderer :: PARAM_SELECTED_TAB => $tab));
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
        else
        {
            throw new NotAllowedException();
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        Manager :: PARAM_ACTION => Manager :: ACTION_BROWSE, 
                        DynamicTabsRenderer :: PARAM_SELECTED_TAB => BrowserComponent :: TAB_REPORT)), 
                Translation :: get('BrowserComponent')));
        
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        self :: PARAM_ACTION => self :: ACTION_BROWSE, 
                        Manager :: PARAM_PUBLICATION_ID => Request :: get(Manager :: PARAM_PUBLICATION_ID), 
                        DynamicTabsRenderer :: PARAM_SELECTED_TAB => BrowserComponent :: TAB_EXPORT_REGISTRATIONS)), 
                Translation :: get('BrowserComponent')));
    }

    function get_parameters()
    {
        return array(Manager :: PARAM_PUBLICATION_ID, self :: PARAM_EXPORT_REGISTRATION_ID);
    }
}
?>