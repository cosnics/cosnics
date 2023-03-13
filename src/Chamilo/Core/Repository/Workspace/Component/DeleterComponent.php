<?php
namespace Chamilo\Core\Repository\Workspace\Component;

use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

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
        $workspaceIdentifiers = $this->getRequest()->getFromPostOrUrl(self::PARAM_WORKSPACE_ID);
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

            $workspaceService = $this->getWorkspaceService();
            $rightsService = $this->getRightsService();

            foreach ($workspaceIdentifiers as $workspaceIdentifier)
            {
                $workspace = $workspaceService->getWorkspaceByIdentifier($workspaceIdentifier);

                if ($rightsService->hasWorkspaceCreatorRights($this->get_user(), $workspace))
                {
                    if (! $workspaceService->deleteWorkspace($workspace))
                    {
                        throw new Exception(
                            Translation::get(
                                'ObjectNotDeleted',
                                array('OBJECT' => Translation::get('Workspace')),
                                StringUtilities::LIBRARIES));
                    }
                    else
                    {
                        $this->getWorkspaceExtensionManager()->workspaceDeleted($workspace, $this->getUser());
                    }
                }
            }

            $success = true;
            $message = Translation::get(
                'ObjectDeleted',
                array('OBJECT' => Translation::get('Workspace')),
                StringUtilities::LIBRARIES);
        }
        catch (Exception $ex)
        {
            $success = false;
            $message = $ex->getMessage();
        }

        $source = $this->getRequest()->get(self::PARAM_BROWSER_SOURCE);
        $returnComponent = isset($source) ? $source : self::ACTION_BROWSE;

        $this->redirectWithMessage($message, ! $success, array(self::PARAM_ACTION => $returnComponent));
    }
}