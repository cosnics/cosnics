<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Component;

use Chamilo\Core\Repository\ContentObject\Presence\Display\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Page;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Component
 * @author Stefan Gabriëls <stefan.gabriels@hogent.be>
 */
class PrintPresenceRegistrationQrComponent extends Manager
{
    /**
     * @throws \Twig\Error\SyntaxError
     * @throws NotAllowedException
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function run(): string
    {
        $this->checkAccessRights();

        Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);
        $this->getApplicationConfiguration()->setEmbeddedApplication(true);

        return $this->getTwig()->render(
            Manager::context() . ':PresenceRegistrationQr.html.twig', [
                'HEADER' => Application::render_header(),
                'FOOTER' => Application::render_footer(),
                'SELF_SERVICE_QR_CODE' => $this->getRegisterPresenceUrl(true),
                'PRESENCE_TITLE' => $this->getPresence()->get_title(),
                'PRESENCE_CONTEXT' => $this->getPresenceServiceBridge()->getContextTitle()
            ]
        );
    }

    /**
     * @throws NotAllowedException
     */
    protected function checkAccessRights()
    {
        if (!$this->getPresenceServiceBridge()->canEditPresence())
        {
            throw new NotAllowedException();
        }
    }
}
