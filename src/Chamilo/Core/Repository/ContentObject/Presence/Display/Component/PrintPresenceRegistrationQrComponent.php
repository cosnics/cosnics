<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Component;

use Chamilo\Core\Repository\ContentObject\Presence\Display\Manager;
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
     */
    public function run(): string
    {
        $this->checkAccessRights();

        Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);

        return $this->getTwig()->render(
            Manager::context() . ':PresenceRegistrationQr.html.twig', [
                'HEADER' => $this->render_header(),
                'FOOTER' => $this->render_footer(),
                'SELF_SERVICE_QR_CODE' => $this->getRegisterPresenceUrl(true)
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
