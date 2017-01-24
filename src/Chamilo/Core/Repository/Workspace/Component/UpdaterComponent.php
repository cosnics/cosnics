<?php
namespace Chamilo\Core\Repository\Workspace\Component;

use Chamilo\Core\Repository\Workspace\Form\WorkspaceForm;
use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\Repository\Workspace\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class UpdaterComponent extends TabComponent
{

    /**
     * Executes this controller
     */
    public function build()
    {
        $workspaceId = Request::get(self::PARAM_WORKSPACE_ID);
        $workspace = DataManager::retrieve_by_id(Workspace::class_name(), $workspaceId);
        
        $form = new WorkspaceForm($this->get_url(array(self::PARAM_WORKSPACE_ID => $workspace->getId())), $workspace);
        
        if ($form->validate())
        {
            try
            {
                $values = $form->exportValues();
                $values[Workspace::PROPERTY_CREATOR_ID] = $workspace->getCreatorId();
                $values[Workspace::PROPERTY_CREATION_DATE] = $workspace->getCreationDate();
                
                $workspaceService = new WorkspaceService(new WorkspaceRepository());
                $success = $workspaceService->updateWorkspace($workspace, $values);
                
                $translation = $success ? 'ObjectUpdated' : 'ObjectNotUpdated';
                
                $message = Translation::get(
                    $translation, 
                    array('OBJECT' => Translation::get('Workspace')), 
                    Utilities::COMMON_LIBRARIES);
            }
            catch (\Exception $ex)
            {
                $success = false;
                $message = $ex->getMessage();
            }

            $source = $this->getRequest()->get(self::PARAM_BROWSER_SOURCE);
            $returnComponent = isset($source) ? $source : self::ACTION_BROWSE;

            $this->redirect($message, ! $success, array(self::PARAM_ACTION => $returnComponent));
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

    /**
     * Adds additional breadcrumbs
     * 
     * @param BreadcrumbTrail $breadcrumb_trail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumb_trail)
    {
        $browserSource = $this->get_parameter(self::PARAM_BROWSER_SOURCE);
        
        $breadcrumb_trail->add(
            new Breadcrumb(
                $this->get_url(array(Manager::PARAM_ACTION => $browserSource)), 
                Translation::get($browserSource . 'Component')));
    }

    public function get_additional_parameters()
    {
        return array(self::PARAM_WORKSPACE_ID, self::PARAM_BROWSER_SOURCE);
    }
}