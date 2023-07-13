<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Table;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager;
use Chamilo\Application\Weblcms\Tool\Manager as ToolManager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\ProgressBarRenderer;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ObjectPublicationTableRenderer extends \Chamilo\Application\Weblcms\Table\ObjectPublicationTableRenderer
{
    public const PROPERTY_PROGRESS = 'progress';

    public function getTableActions(): TableActions
    {
        $tableActions = parent::getTableActions();
        $tableActions->setNamespace(__NAMESPACE__);

        return $tableActions;
    }

    public function get_progress($publication): string
    {
        $toolBrowser = $this->contentObjectPublicationListRenderer->get_tool_browser();

        /** @var TrackingService $trackingService */
        $trackingService = $toolBrowser->get_parent()->createTrackingServiceForPublicationAndCourse(
            $publication[DataClass::PROPERTY_ID], $publication[ContentObjectPublication::PROPERTY_COURSE_ID]
        );

        /** @var User $user */
        $user = $toolBrowser->get_parent()->getUser();

        $learningPath = new LearningPath();
        $learningPath->setId($publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]);

        $progress = $trackingService->getLearningPathProgress(
            $learningPath, $user, $toolBrowser->get_parent()->getCurrentTreeNodeForLearningPath($learningPath)
        );

        if (!is_null($progress))
        {
            $progressBarRenderer = new ProgressBarRenderer();
            $bar = $progressBarRenderer->render($progress);
        }
        else
        {
            $bar = '';
        }

        $url = $this->getUrlGenerator()->fromRequest(
            [
                ToolManager::PARAM_ACTION => ToolManager::ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT,
                ToolManager::PARAM_PUBLICATION_ID => $publication[DataClass::PROPERTY_ID],
                \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_REPORTING
            ]
        );

        return '<a href="' . $url . '">' . $bar . '</a>';
    }

    protected function initializeColumns(): void
    {
        parent::initializeColumns();

        $this->addColumn(
            new StaticTableColumn(
                self::PROPERTY_PROGRESS, $this->getTranslator()->trans('Progress', [], Manager::CONTEXT)
            )
        );
    }

    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $publication): string
    {
        if ($column->get_name() == self::PROPERTY_PROGRESS)
        {
            {
                if (!$this->contentObjectPublicationListRenderer->get_tool_browser()->get_parent()->isEmptyLearningPath(
                    $publication
                ))
                {
                    return $this->get_progress($publication);
                }
                else
                {
                    return $this->getTranslator()->trans('EmptyLearningPath', [], Manager::CONTEXT);
                }
            }
        }

        return parent::renderCell($column, $resultPosition, $publication);
    }

}