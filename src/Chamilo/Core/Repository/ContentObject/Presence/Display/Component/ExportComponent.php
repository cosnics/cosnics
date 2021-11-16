<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Component;

use Chamilo\Core\Repository\ContentObject\Presence\Display\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Component
 * @author Stefan Gabriëls <stefan.gabriels@hogent.be>
 */
class ExportComponent extends Manager
{
    public function run()
    {
        $this->checkAccessRights();
        $presence = $this->getPresence();
        $userIds = $this->getPresenceServiceBridge()->getTargetUserIds();
        $contextIdentifier = $this->getPresenceServiceBridge()->getContextIdentifier();
        $exportService = $this->getExportService();
        $exportService->setTranslator($this->getTranslator());
        $exportService->exportPresence($presence, $userIds, $contextIdentifier);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    protected function checkAccessRights()
    {
        if (!$this->getPresenceServiceBridge()->canEditPresence())
        {
            throw new NotAllowedException();
        }
    }

}