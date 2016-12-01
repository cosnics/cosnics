<?php
namespace Chamilo\Application\Survey\Export\Component;

use Chamilo\Application\Survey\Export\Manager;
use Chamilo\Application\Survey\Export\Storage\DataManager;
use Chamilo\Application\Survey\Service\RightsService;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class RightsEditorComponent extends Manager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $template_ids = Request::get(self::PARAM_EXPORT_TEMPLATE_ID);
        
        $this->set_parameter(self::PARAM_EXPORT_TEMPLATE_ID, $template_ids);
        
        if ($template_ids && ! is_array($template_ids))
        {
            $template_ids = array($template_ids);
        }
        
        $locations = array();
        
        $publication_id = 0;
        
        foreach ($template_ids as $template_id)
        {
            
            $template = DataManager::retrieve_export_template_by_id($template_id);
            
            if ($this->get_user()->is_platform_admin() || $template->get_owner_id() == $this->get_user_id())
            {
                $publication_id = $template->get_publication_id();
                $locations[] = RightsService::getInstance();
            }
        }
        
        \Chamilo\Core\Rights\Editor\Manager::launch($this, self::context(), $locations);
        
        // $user_ids = Rights :: get_allowed_users(Rights :: RIGHT_EXPORT_RESULT, $publication_id, Rights ::
        // TYPE_PUBLICATION);
        // if (count($user_ids) > 0)
        // {
        // $manager->limit_users($user_ids);
        // }
        // else
        // {
        // $manager->limit_users(array(0));
        // }
        
        // $manager->set_types(array(RightsEditorManager :: TYPE_USER));
        // $manager->run();
    }

    function get_available_rights()
    {
        return RightsService::getInstance();
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