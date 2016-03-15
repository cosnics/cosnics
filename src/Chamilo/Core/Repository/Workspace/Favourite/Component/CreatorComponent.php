<?php
namespace Chamilo\Core\Repository\Workspace\Favourite\Component;

use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Repository\Workspace\Favourite\Manager;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Core\Repository\Workspace\Favourite\Service\FavouriteService;
use Chamilo\Core\Repository\Workspace\Favourite\Repository\FavouriteRepository;
use Chamilo\Core\Repository\Workspace\Favourite\Storage\DataClass\WorkspaceUserFavourite;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Rights\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CreatorComponent extends Manager
{

    public function run()
    {
        $workspaceIdentifier = $this->getRequest()->get(
            \Chamilo\Core\Repository\Workspace\Manager :: PARAM_WORKSPACE_ID
        );

        if (!$workspaceIdentifier)
        {
            throw new NoObjectSelectedException(Translation:: get('Workspace'));
        }

        $workspaceService = new WorkspaceService(new WorkspaceRepository());
        $workspace = $workspaceService->getWorkspaceByIdentifier($workspaceIdentifier);

        $favouriteService = new FavouriteService(new FavouriteRepository());
        $workspaceUserFavourite = $favouriteService->createWorkspaceUserFavourite(
            $this->get_user(),
            $workspaceIdentifier
        );

        if ($workspaceUserFavourite instanceof WorkspaceUserFavourite)
        {
            $action = $this->getRequest()->get(\Chamilo\Core\Repository\Workspace\Manager::PARAM_BROWSER_SOURCE);

            $this->redirect(
                Translation:: get(
                    'ObjectCreated',
                    array('OBJECT' => Translation:: get('WorkspaceUserFavourite')),
                    Utilities :: COMMON_LIBRARIES
                ),
                false,
                array(\Chamilo\Core\Repository\Workspace\Manager::PARAM_ACTION => $action),
                array(self::PARAM_ACTION)
            );
        }
        else
        {
            $this->redirect(
                Translation:: get(
                    'ObjectNotCreated',
                    array('OBJECT' => Translation:: get('WorkspaceUserFavourite')),
                    Utilities :: COMMON_LIBRARIES
                ),
                true,
                array(self :: PARAM_ACTION => self :: ACTION_BROWSE)
            );
        }
    }
}