<?php
namespace Chamilo\Core\Repository\Workspace\Component;

use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
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
class DeleterComponent extends Manager
{

    /**
     * Executes this controller
     */
    public function run()
    {
        $workspaceIdentifiers = $this->getRequest()->query->get(self::PARAM_WORKSPACE_ID);
        
        try
        {
            if (empty($workspaceIdentifiers))
            {
                throw new NoObjectSelectedException(Translation::get('Workspace'));
            }
            
            if (! is_array($workspaceIdentifiers))
            {
                $workspaceIdentifiers = array($workspaceIdentifiers);
            }
            
            $workspaceService = new WorkspaceService(new WorkspaceRepository());
            $rightsService = RightsService::getInstance();
            
            foreach ($workspaceIdentifiers as $workspaceIdentifier)
            {
                $workspace = $workspaceService->getWorkspaceByIdentifier($workspaceIdentifier);
                
                if ($rightsService->hasWorkspaceImplementationCreatorRights($this->get_user(), $workspace))
                {
                    if (! $workspaceService->deleteWorkspace($workspace))
                    {
                        throw new \Exception(
                            Translation::get(
                                'ObjectNotDeleted', 
                                array('OBJECT' => Translation::get('Workspace')), 
                                Utilities::COMMON_LIBRARIES));
                    }
                }
            }
            
            $success = true;
            $message = Translation::get(
                'ObjectDeleted', 
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
}