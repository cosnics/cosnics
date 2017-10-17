<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service\ReportingExporter;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ReportingExporter\ExportFormat\TreeNodeChildrenUserProgressExportFormat;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ReportingExporter\ExportFormat\UsersProgressExportFormat;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ReportingExporter\ExportFormat\UserTreeNodeAttemptExportFormat;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ReportingExporter\Writer\WriterInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Platform\Translation;

/**
 * Formats the reporting data to an exportable format that can be processed by the export writer
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Exporter
{
    /**
     * @var TrackingService
     */
    protected $trackingService;

    /**
     * Exporter constructor.
     *
     * @param TrackingService $trackingService
     */
    public function __construct(TrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    /**
     * Exports the users progress
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNodeToExport
     * @param WriterInterface $exportWriter
     */
    public function exportUserProgress(
        LearningPath $learningPath, TreeNode $treeNodeToExport, WriterInterface $exportWriter
    )
    {
        $userProgressExportObjects = [];

        $attemptsWithUsers = $this->trackingService->getTargetUsersWithLearningPathAttempts(
            $learningPath, $treeNodeToExport
        );

        foreach ($attemptsWithUsers as $attemptWithUser)
        {
            $user = new User();
            $user->setId($attemptWithUser['user_id']);

            $progress = $this->trackingService->getLearningPathProgress($learningPath, $user, $treeNodeToExport);

            $userProgressExportObjects[] = new UsersProgressExportFormat(
                $attemptWithUser[User::PROPERTY_LASTNAME],
                $attemptWithUser[User::PROPERTY_FIRSTNAME],
                $attemptWithUser[User::PROPERTY_EMAIL],
                $progress . '%',
                $progress == 100,
                $progress > 0
            );
        }

        $exportWriter->writeExportedObjects($userProgressExportObjects);
    }

    /**
     * Exports the attempts for a user in a given tree node
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNodeToExport
     * @param User $user
     * @param WriterInterface $exportWriter
     */
    public function exportTreeNodeAttemptsForUser(
        LearningPath $learningPath, TreeNode $treeNodeToExport, User $user, WriterInterface $exportWriter
    )
    {
        $userTreeNodeAttemptExportObjects = [];

        $showScore = $treeNodeToExport->supportsScore();

        $treeNodeAttempts = $this->trackingService->getTreeNodeAttempts($learningPath, $user, $treeNodeToExport);
        foreach ($treeNodeAttempts as $treeNodeAttempt)
        {
            $userTreeNodeAttemptExportObjects[] = new UserTreeNodeAttemptExportFormat(
                $treeNodeAttempt->get_start_time(), $treeNodeAttempt->isCompleted(),
                $showScore ? $treeNodeAttempt->get_score() : null, $treeNodeAttempt->get_total_time()
            );
        }

        $exportWriter->writeExportedObjects($userTreeNodeAttemptExportObjects);
    }

    /**
     * Exports the progress for a given user for all the children of a given tree node in a learning path
     *
     * @param LearningPath $learningPath
     * @param TreeNode $treeNodeToExport
     * @param User $user
     * @param WriterInterface $exportWriter
     */
    public function exportTreeNodeChildrenProgressForUser(
        LearningPath $learningPath, TreeNode $treeNodeToExport, User $user, WriterInterface $exportWriter
    )
    {
        $treeNodeChildProgressExportObjects = [];
        $treeNodeChildren = $treeNodeToExport->getChildNodes();

        foreach ($treeNodeChildren as $treeNodeChild)
        {
            $contentObject = $treeNodeChild->getContentObject();

            $treeNodeChildProgressExportObjects[] = new TreeNodeChildrenUserProgressExportFormat(
                Translation::getInstance()->getTranslation(
                    'TypeName', null,
                    ClassnameUtilities::getInstance()->getNamespaceParent($contentObject->context(), 2)
                ),
                $contentObject->get_title(),
                $this->trackingService->isTreeNodeCompleted($learningPath, $user, $treeNodeChild),
                $this->trackingService->getAverageScoreInTreeNode($learningPath, $user, $treeNodeChild),
                $this->trackingService->getMaximumScoreInTreeNode($learningPath, $user, $treeNodeChild),
                $this->trackingService->getMinimumScoreInTreeNode($learningPath, $user, $treeNodeChild),
                $this->trackingService->getLastAttemptScoreForTreeNode($learningPath, $user, $treeNodeChild),
                $this->trackingService->getTotalTimeSpentInTreeNode($learningPath, $user, $treeNodeChild)
            );
        }

        $exportWriter->writeExportedObjects($treeNodeChildProgressExportObjects);
    }
}