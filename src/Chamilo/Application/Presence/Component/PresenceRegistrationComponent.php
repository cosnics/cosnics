<?php

namespace Chamilo\Application\Presence\Component;

use Chamilo\Application\Presence\Manager;
use Chamilo\Application\Presence\Service\PresenceRegistrationService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Page;

/**
 * @package Chamilo\Application\Presence\Component
 * @author - Sven Vanpoucke - Hogeschool Gent
 *
 * TODO: verify URL (protected domain name)
 */
class PresenceRegistrationComponent extends Manager
{
    /**
     * @return string|void
     * @throws NotAllowedException
     */
    function run()
    {
        Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);

        $publicationId = $this->getRequest()->getFromUrl(self::PARAM_PUBLICATION_ID);
        $treeNodeId = $this->getRequest()->getFromUrl(self::PARAM_TREE_NODE_ID);

        $securityKey = $this->getRequest()->getFromUrl(self::PARAM_SECURITY_KEY);

        if (empty($publicationId) || empty($securityKey))
        {
            throw new NotAllowedException();
        }

        try
        {
            $userRegistrationEntry = $this->getPresenceRegistrationService()->registerUserInPresence(
                $this->getUser(), $publicationId, $treeNodeId, $securityKey
            );

            $isUserInPresenceList =
                $this->getPresenceRegistrationService()->isUserInPresenceList($this->getUser(), $publicationId);

            return $this->getTwig()->render(
                Manager::context() . ':PresenceRegistration.html.twig',
                [
                    'HEADER' => $this->render_header(), 'FOOTER' => $this->render_footer(),
                    'REGISTRATION_ENTRY' => $userRegistrationEntry, 'USER_IN_PRESENCE_LIST' => $isUserInPresenceList,
                    'USER' => $this->getUser(), 'USER_PICTURE_URL' => $this->getUserPictureUrl()
                ]
            );
        }
        catch (\Exception $exception)
        {
            $this->getExceptionLogger()->logException($exception);
            throw new NotAllowedException();
        }
    }

    /**
     * @return PresenceRegistrationService
     */
    protected function getPresenceRegistrationService()
    {
        return $this->getService(PresenceRegistrationService::class);
    }

    protected function getUserPictureUrl()
    {
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager::ACTION_USER_PICTURE,
                \Chamilo\Core\User\Manager::PARAM_USER_USER_ID => $this->getUser()->getId()
            )
        );

        return $redirect->getUrl();
    }
}
