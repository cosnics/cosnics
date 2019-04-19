<?php

namespace Chamilo\Core\Repository\ContentObject\ExternalTool\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display;

use Chamilo\Core\Repository\ContentObject\ExternalTool\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\ExternalToolServiceBridge;
use Chamilo\Core\Repository\ContentObject\ExternalTool\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\ExternalToolServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder\Type\ContentObjectEmbedder;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;

/**
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class Embedder extends ContentObjectEmbedder
{
    use DependencyInjectionContainerTrait;

    /**
     * @return string
     */
    public function run()
    {
        $this->initializeContainer();

        $configuration = new ApplicationConfiguration(
            $this->get_application()->getRequest(), $this->get_application()->getUser(), $this->get_application()
        );

        $attempts = $this->trackingService->getTreeNodeAttempts(
            $this->learningPath, $this->get_application()->getUser(), $this->treeNode
        );

        if (empty($attempts))
        {
            $activeAttempt = $this->trackingService->getActiveAttempt(
                $this->learningPath, $this->treeNode, $this->get_application()->getUser()
            );
        }
        else
        {
            $activeAttempt = $attempts[0];
        }

        $this->buildBridgeServices($activeAttempt);

        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Repository\ContentObject\ExternalTool\Display\Manager::context(),
            $configuration
        )->run();
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt $activeAttempt
     */
    protected function buildBridgeServices(TreeNodeAttempt $activeAttempt)
    {
        $learningPathExternalToolBridge = new ExternalToolServiceBridge(
            $this->getBridgeManager()->getBridgeByInterface(ExternalToolServiceBridgeInterface::class)
        );

        $learningPathExternalToolBridge->setTreeNode($this->treeNode);
        $learningPathExternalToolBridge->setTreeNodeAttempt($activeAttempt);

        $this->getBridgeManager()->addBridge($learningPathExternalToolBridge);
    }

    /**
     * @return bool
     */
    public function supportMultipleAttempts()
    {
        return false;
    }
}
