<?php
namespace Chamilo\Core\Repository\ContentObject\Evaluation\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\EvaluationServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\EvaluationEntryService;
use Chamilo\Core\Repository\ContentObject\Evaluation\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\EvaluationServiceBridge;
use Chamilo\Core\Repository\ContentObject\Evaluation\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\LearningPathEvaluationServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder\Type\ContentObjectEmbedder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\ApplicationFactory;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package core\repository\content_object\page\integration\core\repository\content_object\learning_path\display
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
        $applicationFactory->setEvaluationServiceBridge(
            $this->getBridgeManager()->getBridgeByInterface(EvaluationServiceBridgeInterface::class)
        );
        return $applicationFactory->getApplication(
            \Chamilo\Core\Repository\ContentObject\Evaluation\Display\Manager::context(),
            $configuration
        )->run();
    }

    /**
     * @param TreeNodeAttempt $activeAttempt
     */
    protected function buildBridgeServices(TreeNodeAttempt $activeAttempt)
    {
        /** @var LearningPathEvaluationServiceBridgeInterface $learningPathEvaluationServiceBridge */
        $learningPathEvaluationServiceBridge =
            $this->getBridgeManager()->getBridgeByInterface(LearningPathEvaluationServiceBridgeInterface::class);

        $evaluationServiceBridge = new EvaluationServiceBridge($learningPathEvaluationServiceBridge, $this->getService(EvaluationEntryService::class), $this->getService(TreeNodeDataService::class));
        $evaluationServiceBridge->setTreeNode($this->treeNode);
        $evaluationServiceBridge->setTreeNodeAttempt($activeAttempt);

        $this->getBridgeManager()->addBridge($evaluationServiceBridge);
    }

    /**
     * @return ApplicationFactory
     */
    protected function getApplicationFactory()
    {
        return new ApplicationFactory($this->getRequest(), StringUtilities::getInstance(), Translation::getInstance());
    }
}
