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
    const PARAM_SELECTED_ENTITY = 'SelectedLPAssignmentEntity';

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

        $bridge = $this->getBridgeManager()->getBridgeByInterface(EvaluationServiceBridgeInterface::class);

        $applicationFactory = $this->getApplicationFactory();
        $applicationFactory->setEvaluationServiceBridge($bridge);

        $this->addSelectedEntityToApplicationFactory($applicationFactory, $bridge);
        $this->registerSelectedEntity($bridge);

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
     * @param ApplicationFactory $applicationFactory
     */
    protected function addSelectedEntityToApplicationFactory(
        ApplicationFactory $applicationFactory, EvaluationServiceBridge $evaluationServiceBridge
    )
    {
        if ($this->getRequest()->getFromPostOrUrl(
                \Chamilo\Core\Repository\ContentObject\Evaluation\Display\Manager::PARAM_ACTION
            ) == \Chamilo\Core\Repository\ContentObject\Evaluation\Display\Manager::ACTION_ENTRY
        )
        {
            return null;
        }

        $selectedEntity = $this->getSessionUtilities()->get(self::PARAM_SELECTED_ENTITY);
        if (!is_array($selectedEntity) || !array_key_exists($this->getLPIdentifier(), $selectedEntity))
        {
            return;
        }

        $entityData = $selectedEntity[$this->getLPIdentifier()];
        $entityType = $entityData['entity_type'];
        $entityId = $entityData['entity_id'];

        if (is_null($entityType) || $entityType != $evaluationServiceBridge->getCurrentEntityType() || empty($entityId))
        {
            return;
        }

        $applicationFactory->setViewEntity($entityId);
    }

    protected function registerSelectedEntity(EvaluationServiceBridge $evaluationServiceBridge): void
    {
        if ($this->getRequest()->getFromPostOrUrl(
                \Chamilo\Core\Repository\ContentObject\Evaluation\Display\Manager::PARAM_ACTION
            ) == \Chamilo\Core\Repository\ContentObject\Evaluation\Display\Manager::ACTION_ENTRY
        )
        {
            $entityId = $this->getRequest()->getFromPostOrUrl(
                \Chamilo\Core\Repository\ContentObject\Evaluation\Display\Manager::PARAM_ENTITY_ID
            );

            $entityType = $evaluationServiceBridge->getCurrentEntityType();

            $this->getSessionUtilities()->register(
                self::PARAM_SELECTED_ENTITY,
                [$this->getLPIdentifier() => ['entity_type' => $entityType, 'entity_id' => $entityId]]
            );
        }
    }

    public function getLPIdentifier()
    {
        $publicationId = $this->getRequest()->getFromPostOrUrl(\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION);

        if($publicationId)
        {
            return 'p' . $publicationId;
        }

        return 'lp' . $this->learningPath->getId();
    }

    /**
     * @return ApplicationFactory
     */
    protected function getApplicationFactory()
    {
        return new ApplicationFactory($this->getRequest(), StringUtilities::getInstance(), Translation::getInstance());
    }
}
