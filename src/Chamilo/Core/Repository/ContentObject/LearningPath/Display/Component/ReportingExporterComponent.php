<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ReportingExporter\Exporter;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ReportingExporter\Writer\CsvWriter;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Filesystem;

/**
 * Exports the reporting data
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ReportingExporterComponent extends BaseReportingComponent
{
    const PARAM_EXPORT = 'export';

    const EXPORT_USER_PROGRESS = 'UserProgress';
    const EXPORT_TREE_NODE_ATTEMPTS = 'TreeNodeAttempts';
    const EXPORT_TREE_NODE_CHILDREN_PROGRESS = 'TreeNodeChildrenProgress';
    const EXPORT_SCORE_OVERVIEW = 'ScoreOverviewExport';

    /**
     * Runs this component
     */
    public function run()
    {
        $pathBuilder = $this->getConfigurablePathBuilder();
        $temporaryDirectory = $pathBuilder->getTemporaryPath(Manager::context());
        Filesystem::create_dir($temporaryDirectory);

        $exporter = new Exporter(
            $this->getTrackingService(), $this->getAutomaticNumberingService(), $this->getLearningPathService()
        );

        $exportMode = $this->getRequest()->get(self::PARAM_EXPORT);
        $this->validateExportMode($exportMode);

        $file = $temporaryDirectory . uniqid() . '.csv';
        $filename = '';
        $csvWriter = new CsvWriter($file);

        switch ($exportMode)
        {
            case self::EXPORT_USER_PROGRESS:
                if (!$this->canViewReporting())
                {
                    throw new NotAllowedException();
                }

                $exporter->exportUserProgress($this->learningPath, $this->getCurrentTreeNode(), $csvWriter);
                $filename = 'Progress.csv';
                break;
            case self::EXPORT_TREE_NODE_ATTEMPTS:
                $exporter->exportTreeNodeAttemptsForUser(
                    $this->learningPath, $this->getCurrentTreeNode(), $this->getReportingUser(), $csvWriter
                );
                $filename = 'Attempts.csv';
                break;
            case self::EXPORT_TREE_NODE_CHILDREN_PROGRESS:

                $exporter->exportTreeNodeChildrenProgressForUser(
                    $this->learningPath, $this->getCurrentTreeNode(), $this->getReportingUser(), $csvWriter
                );
                $filename = 'UserProgress.csv';
                break;
            case self::EXPORT_SCORE_OVERVIEW:
                $exporter->exportScoreOverviewForUser(
                    $this->learningPath, $this->getTree(), $this->getReportingUser(), $csvWriter
                );
                $filename = 'ScoreOverview.csv';
                break;
        }

        Filesystem::file_send_for_download($file, false, $filename, 'text/csv');
        Filesystem::remove($file);
    }

    function build()
    {
    }

    /**
     * Validates the given export mode
     *
     * @param string $exportMode
     *
     * @throws \InvalidArgumentException
     * @throws NotAllowedException
     */
    protected function validateExportMode($exportMode)
    {
        $exportModes = [
            self::EXPORT_USER_PROGRESS, self::EXPORT_TREE_NODE_ATTEMPTS, self::EXPORT_TREE_NODE_CHILDREN_PROGRESS,
            self::EXPORT_SCORE_OVERVIEW
        ];

        if (!in_array($exportMode, $exportModes))
        {
            throw new \InvalidArgumentException(sprintf('The given export mode %s is not supported', $exportMode));
        }

        /*if ($exportMode == self::EXPORT_USER_PROGRESS && !$this->canEditCurrentTreeNode())
        {
            throw new NotAllowedException();
        }*/
    }
}