<?php
namespace Chamilo\Core\Repository\Workspace\Component;

use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\NotificationMessage\NotificationMessage;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package Chamilo\Core\Repository\Workspace\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DefaultComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function run()
    {
        if (!$this->getWorkspaceIdentifier())
        {
            throw new NoObjectSelectedException($this->getTranslator()->trans('Workspace', [], Manager::CONTEXT));
        }

        if (!$this->getRightsService()->isWorkspaceCreatorByWorkspaceIdentifier(
            $this->getUser(), $this->getWorkspaceIdentifier()
        ))
        {
            throw new NotAllowedException();
        }

        if (!$this->getWorkspaceService()->saveWorkspaceUserDefaultForWorkspaceIdentifierAndUserIdentifier(
            $this->getWorkspaceIdentifier(), $this->getUser()->getId()
        ))
        {
            $translationVariable = 'ObjectNotCreated';
            $messageType = NotificationMessage::TYPE_DANGER;
        }
        else
        {
            $translationVariable = 'ObjectCreated';
            $messageType = NotificationMessage::TYPE_INFO;
        }

        $translator = $this->getTranslator();

        $this->getNotificationMessageManager()->addMessage(
            new NotificationMessage(
                $translator->trans(
                    $translationVariable, [
                    'OBJECT' => $translator->trans('WorkspaceUserDefault', [],
                        \Chamilo\Core\Repository\Workspace\Favourite\Manager::CONTEXT)
                ], StringUtilities::LIBRARIES
                ), $messageType
            )
        );

        $redirectUrl = $this->getUrlGenerator()->fromParameters([
            Application::PARAM_CONTEXT => Manager::CONTEXT,
            Manager::PARAM_ACTION => Manager::ACTION_BROWSE_PERSONAL
        ]);

        return new RedirectResponse($redirectUrl);
    }

    public function getWorkspaceIdentifier(): ?string
    {
        return $this->getRequest()->query->get(self::PARAM_WORKSPACE_ID, null);
    }
}
