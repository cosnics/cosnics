<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service\ReportingExporter;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ReportingExporter\ExportFormat\UsersProgressExportFormat;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ReportingExporter\Writer\WriterInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\User\Storage\DataClass\User;

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

        $totalNodes = count($treeNodeToExport->getDescendantNodes()) + 1;

        $attemptsWithUsers = $this->trackingService->getLearningPathAttemptsWithUser($learningPath, $treeNodeToExport);
        foreach($attemptsWithUsers as $attemptWithUser)
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
}