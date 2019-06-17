<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview\Component;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview\Bridge\AssignmentServiceBridge;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview\Bridge\EntryPlagiarismResultServiceBridge;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview\Bridge\EphorusServiceBridge;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview\Bridge\FeedbackServiceBridge;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview\Bridge\NotificationServiceBridge;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ViewerComponent extends Manager
{

    public function run()
    {
        $this->initializeBridges();

        $configuration = new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this);

        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::context(),
            $configuration
        )->run();
    }

    public function initializeBridges()
    {
        $this->getBridgeManager()->addBridge(new AssignmentServiceBridge());
        $this->getBridgeManager()->addBridge(new FeedbackServiceBridge());
        $this->getBridgeManager()->addBridge(new NotificationServiceBridge());
        $this->getBridgeManager()->addBridge(new EphorusServiceBridge());
        $this->getBridgeManager()->addBridge(new EntryPlagiarismResultServiceBridge());
    }
}
