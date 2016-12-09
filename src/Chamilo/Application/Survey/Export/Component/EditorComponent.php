<?php
namespace Chamilo\Application\Survey\Export\Component;

use Chamilo\Application\Survey\Export\Form\ExportTemplateForm;
use Chamilo\Application\Survey\Export\Manager;
use Chamilo\Application\Survey\Export\Storage\DataManager;
use Chamilo\Application\Survey\Service\RightsService;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class EditorComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $publication_id = Request::get(Manager::PARAM_PUBLICATION_ID);
        $id = Request::get(self::PARAM_EXPORT_TEMPLATE_ID);
        
        if (RightsService::getInstance())
        {
            
            $template = DataManager::retrieve_export_template_by_id($id);
            
            $form = new ExportTemplateForm(ExportTemplateForm::TYPE_EDIT, $this->get_url(), $template);
            
            if ($form->validate())
            {
                $success = $form->update();
                if ($success)
                {
                    $message = 'SelectedExportTemplateChanged';
                }
                else
                {
                    $message = 'SelectedExportTemplateNotChanged';
                }
                
                $this->redirect(
                    Translation::get($message), 
                    ! $success, 
                    array(
                        self::PARAM_ACTION => self::ACTION_BROWSE, 
                        Manager::PARAM_PUBLICATION_ID => $publication_id, 
                        DynamicTabsRenderer::PARAM_SELECTED_TAB => BrowserComponent::TAB_EXPORT_TEMPLATES));
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

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_BROWSE, 
                        DynamicTabsRenderer::PARAM_SELECTED_TAB => BrowserComponent::TAB_EXPORT)), 
                Translation::get('BrowserComponent')));
        
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_BROWSE, 
                        Manager::PARAM_PUBLICATION_ID => Request::get(Manager::PARAM_PUBLICATION_ID), 
                        DynamicTabsRenderer::PARAM_SELECTED_TAB => BrowserComponent::TAB_EXPORT_TEMPLATES)), 
                Translation::get('BrowserComponent')));
    }

    function get_parameters()
    {
        return array(Manager::PARAM_PUBLICATION_ID, self::PARAM_EXPORT_TEMPLATE_ID);
    }
}
?>