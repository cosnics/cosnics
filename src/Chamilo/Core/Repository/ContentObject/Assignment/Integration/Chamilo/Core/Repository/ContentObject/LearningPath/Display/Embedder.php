<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\ApplicationFactory;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\AssignmentServiceBridge;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\EntryPlagiarismResultServiceBridge;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\EphorusServiceBridge;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\FeedbackServiceBridge;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\AssignmentServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\EphorusServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\FeedbackServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\NotificationServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\NotificationServiceBridge;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder\Type\ContentObjectEmbedder;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package core\repository\content_object\assignment\integration\core\repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Embedder extends ContentObjectEmbedder
{
    use DependencyInjectionContainerTrait;

    /**
     *
     * @see \core\repository\content_object\learning_path\display\Embedder::run()
     */
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

        $applicationFactory = $this->getApplicationFactory();
        $applicationFactory->setAssignmentServiceBridge(
            $this->getBridgeManager()->getBridgeByInterface(
                \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface::class
            )
        );

        return $applicationFactory->getApplication(
            \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::context(),
            $configuration
        )->run();
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt $activeAttempt
     */
    protected function buildBridgeServices(TreeNodeAttempt $activeAttempt)
    {
        /** @var AssignmentServiceBridgeInterface $learningPathAssignmentServiceBridge */
        $learningPathAssignmentServiceBridge =
            $this->getBridgeManager()->getBridgeByInterface(AssignmentServiceBridgeInterface::class);

        $assignmentServiceBridge = new AssignmentServiceBridge($learningPathAssignmentServiceBridge);
        $assignmentServiceBridge->setTreeNode($this->treeNode);
        $assignmentServiceBridge->setTreeNodeAttempt($activeAttempt);

        /** @var FeedbackServiceBridgeInterface $learningPathFeedbackServiceBridge */
        $learningPathFeedbackServiceBridge =
            $this->getBridgeManager()->getBridgeByInterface(FeedbackServiceBridgeInterface::class);
        $feedbackServiceBridge = new FeedbackServiceBridge($learningPathFeedbackServiceBridge);
        $feedbackServiceBridge->setTreeNode($this->treeNode);

        /** @var EphorusServiceBridgeInterface $learningPathEphorusServiceBridge */
        $learningPathEphorusServiceBridge =
            $this->getBridgeManager()->getBridgeByInterface(EphorusServiceBridgeInterface::class);
        $ephorusServiceBridge = new EphorusServiceBridge($learningPathEphorusServiceBridge);
        $ephorusServiceBridge->setTreeNode($this->treeNode);

        /** @var \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\NotificationServiceBridgeInterface $learningPathNotificationServiceBridge */
        $learningPathNotificationServiceBridge =
            $this->getBridgeManager()->getBridgeByInterface(NotificationServiceBridgeInterface::class);
        $notificationServiceBridge = new NotificationServiceBridge($learningPathNotificationServiceBridge);
        $notificationServiceBridge->setTreeNode($this->treeNode);

        /** @var EntryPlagiarismResultServiceBridgeInterface  $learningPathEntryPlagiarismResultServiceBridge */
        $learningPathEntryPlagiarismResultServiceBridge =
            $this->getBridgeManager()->getBridgeByInterface(EntryPlagiarismResultServiceBridgeInterface::class);

        $entryPlagiarismResultServiceBridge = new EntryPlagiarismResultServiceBridge($learningPathEntryPlagiarismResultServiceBridge);
        $entryPlagiarismResultServiceBridge->setTreeNode($this->treeNode);

        $this->getBridgeManager()->addBridge($assignmentServiceBridge);
        $this->getBridgeManager()->addBridge($feedbackServiceBridge);
        $this->getBridgeManager()->addBridge($ephorusServiceBridge);
        $this->getBridgeManager()->addBridge($notificationServiceBridge);
        $this->getBridgeManager()->addBridge($entryPlagiarismResultServiceBridge);
    }

    /**
     * @return \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    protected function getDataClassRepository()
    {
        return $this->get_application()->getService(
            'chamilo.libraries.storage.data_manager.doctrine.data_class_repository'
        );
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\ApplicationFactory
     */
    protected function getApplicationFactory()
    {
        return new ApplicationFactory($this->getRequest(), StringUtilities::getInstance(), Translation::getInstance());
    }

}
