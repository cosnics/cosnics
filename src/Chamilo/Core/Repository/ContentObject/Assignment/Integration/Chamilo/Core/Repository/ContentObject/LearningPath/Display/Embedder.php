<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\LearningPathAssignmentService;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository\LearningPathAssignmentRepository;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Service\AssignmentDataProvider;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder\Type\ContentObjectEmbedder;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;

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
     * @return string
     */
    public function render()
    {
        $this->initializeContainer();

        $configuration = new ApplicationConfiguration(
            $this->get_application()->getRequest(), $this->get_application()->getUser(), $this->get_application()
        );

        $assignmentDataProvider = new AssignmentDataProvider(
            $this->get_application()->getTranslator(),
            new LearningPathAssignmentService(new LearningPathAssignmentRepository($this->getDataClassRepository()))
        );

        $activeAttempt = $this->trackingService->getActiveAttempt(
            $this->learningPath, $this->treeNode, $this->get_application()->getUser()
        );

        /** @var \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TrackingParametersInterface $trackingParameters * */
        $trackingParameters = $this->get_application()->get_parent()->getTrackingParameters();

        $assignmentDataProvider->setTreeNode($this->treeNode);
        $assignmentDataProvider->setTreeNodeAttempt($activeAttempt);
        $assignmentDataProvider->setCanEditAssignment($this->get_application()->canEditCurrentTreeNode());
        $assignmentDataProvider->setTargetUserIds(
            $trackingParameters->getLearningPathTargetUserIds($this->learningPath)
        );

        $configuration->set(
            \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::CONFIGURATION_DATA_PROVIDER,
            $assignmentDataProvider
        );

        $applicationFactory = $this->getApplicationFactory();

        return $applicationFactory->getApplication(
            \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::context(),
            $configuration
        )->run();
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

}
