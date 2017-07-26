<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ReportingExporter\Exporter;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ReportingExporter\Writer\CsvWriter;
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

    /**
     * Runs this component
     */
    public function run()
    {
        $pathBuilder = $this->getConfigurablePathBuilder();
        $temporaryDirectory = $pathBuilder->getTemporaryPath(Manager::context());
        Filesystem::create_dir($temporaryDirectory);

        $exporter = new Exporter($this->getTrackingService());

        $exportMode = $this->getRequest()->get(self::PARAM_EXPORT);
        $this->validateExportMode($exportMode);

        $exporter->exportUserProgress(
            $this->learningPath, $this->getCurrentTreeNode(),
            new CsvWriter($temporaryDirectory . 'UsersProgressExport.csv')
        );

        $exporter->exportTreeNodeAttemptsForUser(
            $this->learningPath, $this->getCurrentTreeNode(), $this->getReportingUser(),
            new CsvWriter($temporaryDirectory . 'TreeNodeAttempts.csv')
        );

        $exporter->exportTreeNodeChildrenProgressForUser(
            $this->learningPath, $this->getCurrentTreeNode(), $this->getReportingUser(),
            new CsvWriter($temporaryDirectory . 'TreeNodeChildrenProgress.csv')
        );
//        Filesystem::file_send_for_download($filename, false, null, 'text/csv');
    }

    function build()
    {
    }

    protected function validateExportMode($exportMode)
    {
        $exportModes =
            [self::EXPORT_USER_PROGRESS, self::EXPORT_TREE_NODE_ATTEMPTS, self::EXPORT_TREE_NODE_CHILDREN_PROGRESS];

        if(!in_array($exportMode, $exportModes))
        {
            
        }
    }
}