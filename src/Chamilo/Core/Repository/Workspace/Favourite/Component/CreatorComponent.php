<?php
namespace Chamilo\Core\Repository\Workspace\Favourite\Component;

use Chamilo\Core\Repository\Workspace\Favourite\Manager;
use Chamilo\Core\Repository\Workspace\Favourite\Storage\DataClass\WorkspaceUserFavourite;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Format\NotificationMessage\NotificationMessage;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;
use RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package Chamilo\Core\Repository\Workspace\Favourite\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CreatorComponent extends Manager
{

    public function run()
    {
        $workspaceIdentifiers = $this->getRequest()->getFromRequestOrQuery(
            \Chamilo\Core\Repository\Workspace\Manager::PARAM_WORKSPACE_ID
        );

        try
        {
            if (empty($workspaceIdentifiers))
            {
                throw new NoObjectSelectedException(Translation::get('Workspace'));
            }

            if (!is_array($workspaceIdentifiers))
            {
                $workspaceIdentifiers = [$workspaceIdentifiers];
            }

            $favouriteService = $this->getFavouriteService();

            foreach ($workspaceIdentifiers as $workspaceIdentifier)
            {
                $workspaceUserFavourite = $favouriteService->createWorkspaceUserFavourite(
                    $this->get_user(), $workspaceIdentifier
                );

                if (!$workspaceUserFavourite instanceof WorkspaceUserFavourite)
                {
                    throw new RuntimeException(
                        Translation::getInstance()->getTranslation(
                            'CouldNotCreateWorkspaceFavorite', $workspaceIdentifier, null, Manager::context()
                        )
                    );
                }
            }
        }
        catch (Exception $ex)
        {
            $this->redirectWithMessage(
                Translation::get(
                    'ObjectNotCreated', ['OBJECT' => Translation::get('WorkspaceUserFavourite')],
                    StringUtilities::LIBRARIES
                ), true, [\Chamilo\Core\Repository\Workspace\Manager::PARAM_ACTION => $action], [self::PARAM_ACTION]
            );
        }

        $translator = $this->getTranslator();

        $this->getNotificationMessageManager()->addMessage(
            new NotificationMessage(
                $translator->trans(
                    'ObjectCreated', ['OBJECT' => $translator->trans('WorkspaceUserFavourite', [], Manager::CONTEXT)],
                    StringUtilities::LIBRARIES
                )
            )
        );

        $redirectUrl = $this->getUrlGenerator()->fromParameters([
            Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Workspace\Manager::CONTEXT,
            \Chamilo\Core\Repository\Workspace\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Manager::ACTION_FAVOURITE
        ]);

        return new RedirectResponse($redirectUrl);
    }
}