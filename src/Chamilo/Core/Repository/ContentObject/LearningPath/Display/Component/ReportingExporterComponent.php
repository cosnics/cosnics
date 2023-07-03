<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ReportingExporter\Exporter;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ReportingExporter\Writer\CsvWriter;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use InvalidArgumentException;

/**
 * Exports the reporting data
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ReportingExporterComponent extends BaseReportingComponent
{
    public const EXPORT_TREE_NODE_ATTEMPTS = 'TreeNodeAttempts';

    public const EXPORT_TREE_NODE_CHILDREN_PROGRESS = 'TreeNodeChildrenProgress';

    public const EXPORT_USER_PROGRESS = 'UserProgress';

    public const PARAM_EXPORT = 'export';

    /**
     * Runs this component
     */
    public function run()
    {
        $filesystem = $this->getFilesystem();
        $pathBuilder = $this->getConfigurablePathBuilder();
        $temporaryDirectory = $pathBuilder->getTemporaryPath(Manager::CONTEXT);

        $filesystem->mkdir($temporaryDirectory);

        $exporter = new Exporter($this->getTrackingService());

        $exportMode = $this->getRequest()->getFromRequestOrQuery(self::PARAM_EXPORT);
        $this->validateExportMode($exportMode);

        $file = $temporaryDirectory . uniqid() . '.csv';
        $filename = '';
        $csvWriter = new CsvWriter($file);

        switch ($exportMode)
        {
            case self::EXPORT_USER_PROGRESS:
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
        }

        $this->getFilesystemTools()->sendFileForDownload($file, $filename, 'text/csv');
        $filesystem->remove($file);
    }

    public function build()
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
        $exportModes =
            [self::EXPORT_USER_PROGRESS, self::EXPORT_TREE_NODE_ATTEMPTS, self::EXPORT_TREE_NODE_CHILDREN_PROGRESS];

        if (!in_array($exportMode, $exportModes))
        {
            throw new InvalidArgumentException(sprintf('The given export mode %s is not supported', $exportMode));
        }

        if ($exportMode == self::EXPORT_USER_PROGRESS && !$this->canEditCurrentTreeNode())
        {
            throw new NotAllowedException();
        }
    }
}