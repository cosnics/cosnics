<?php
namespace Chamilo\Core\Repository\Workspace\Favourite\Component;

use Chamilo\Core\Repository\Workspace\Favourite\Manager;
use Chamilo\Core\Repository\Workspace\Favourite\Repository\FavouriteRepository;
use Chamilo\Core\Repository\Workspace\Favourite\Service\FavouriteService;
use Chamilo\Core\Repository\Workspace\Favourite\Storage\DataClass\WorkspaceUserFavourite;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Platform\Translation;
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
        $workspaceIdentifiers = $this->getRequest()->get(
            \Chamilo\Core\Repository\Workspace\Manager :: PARAM_WORKSPACE_ID);

        $action = $this->getRequest()->get(\Chamilo\Core\Repository\Workspace\Manager :: PARAM_BROWSER_SOURCE);

        try
        {
            if (empty($workspaceIdentifiers))
            {
                throw new NoObjectSelectedException(Translation :: get('Workspace'));
            }

            if (! is_array($workspaceIdentifiers))
            {
                $workspaceIdentifiers = array($workspaceIdentifiers);
            }

            $favouriteService = new FavouriteService(new FavouriteRepository());

            foreach ($workspaceIdentifiers as $workspaceIdentifier)
            {
                $workspaceUserFavourite = $favouriteService->createWorkspaceUserFavourite(
                    $this->get_user(),
                    $workspaceIdentifier);

                if (! $workspaceUserFavourite instanceof WorkspaceUserFavourite)
                {
                    throw new \RuntimeException(
                        Translation :: getInstance()->getTranslation(
                            'CouldNotCreateWorkspaceFavorite',
                            $workspaceIdentifier,
                            null,
                            Manager :: context()));
                }
            }
        }
        catch (\Exception $ex)
        {
            $this->redirect(
                Translation :: get(
                    'ObjectNotCreated',
                    array('OBJECT' => Translation :: get('WorkspaceUserFavourite')),
                    Utilities :: COMMON_LIBRARIES),
                true,
                array(\Chamilo\Core\Repository\Workspace\Manager :: PARAM_ACTION => $action),
                array(self :: PARAM_ACTION));
        }

        $this->redirect(
            Translation :: get(
                'ObjectCreated',
                array('OBJECT' => Translation :: get('WorkspaceUserFavourite', null, Manager :: context())),
                Utilities :: COMMON_LIBRARIES),
            false,
            array(\Chamilo\Core\Repository\Workspace\Manager :: PARAM_ACTION => $action),
            array(self :: PARAM_ACTION));
    }
}