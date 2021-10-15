<?php
namespace Chamilo\Core\Repository\ContentObject\Presence\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display;

use Chamilo\Core\Repository\ContentObject\Presence\Display\Bridge\Interfaces\PresenceServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Presence\Display\Service\PresenceService;
use Chamilo\Core\Repository\ContentObject\Presence\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\PresenceServiceBridge;
use Chamilo\Core\Repository\ContentObject\Presence\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\LearningPathPresenceServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder\Type\ContentObjectEmbedder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Core\Repository\ContentObject\Presence\Display\ApplicationFactory;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Embedder extends ContentObjectEmbedder
{
    use DependencyInjectionContainerTrait;

    public function run()
    {
        $this->initializeContainer();

        $configuration = new ApplicationConfiguration(
            $this->get_application()->getRequest(), $this->get_application()->getUser(), $this->get_application()
        );

        $activeAttempt = $this->trackingService->getActiveAttempt(
            $this->learningPath, $this->treeNode, $this->get_application()->getUser()
        );

        $this->buildBridgeServices($activeAttempt);

        $this->trackingService->setActiveAttemptCompleted($this->learningPath, $this->treeNode, $this->get_application()->getUser());

        $applicationFactory = $this->getApplicationFactory();
        $applicationFactory->setPresenceServiceBridge(
            $this->getBridgeManager()->getBridgeByInterface(PresenceServiceBridgeInterface::class)
        );
        return $applicationFactory->getApplication(
            \Chamilo\Core\Repository\ContentObject\Presence\Display\Manager::context(),
            $configuration
        )->run();
    }

    /**
     * @param TreeNodeAttempt $activeAttempt
     */
    protected function buildBridgeServices(TreeNodeAttempt $activeAttempt)
    {
        /** @var LearningPathPresenceServiceBridgeInterface $learningPathPresenceServiceBridge */
        $learningPathPresenceServiceBridge =
            $this->getBridgeManager()->getBridgeByInterface(LearningPathPresenceServiceBridgeInterface::class);

        $presenceServiceBridge = new PresenceServiceBridge($learningPathPresenceServiceBridge, $this->getService(PresenceService::class), $this->getService(TreeNodeDataService::class));
        $presenceServiceBridge->setTreeNode($this->treeNode);
        $presenceServiceBridge->setTreeNodeAttempt($activeAttempt);
        $presenceServiceBridge->setLearningPath($this->learningPath);

        $this->getBridgeManager()->addBridge($presenceServiceBridge);
    }

    /**
     * @return ApplicationFactory
     */
    protected function getApplicationFactory()
    {
        return new ApplicationFactory($this->getRequest(), StringUtilities::getInstance(), Translation::getInstance());
    }
}
