<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Table\Publication;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Table\Publication\Table\ObjectPublicationTableCellRenderer;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\ProgressBarRenderer;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\String\Text;

/**
 * Extension on the content object publication table cell renderer for this tool
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PublicationTableCellRenderer extends ObjectPublicationTableCellRenderer
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Renders a cell for a given object
     *
     * @param $column \libraries\ObjectTableColumn
     *
     * @param mixed $publication
     *
     * @return String
     */
    public function render_cell($column, $publication)
    {
        switch ($column->get_name())
        {
            case PublicationTableColumnModel::COLUMN_PROGRESS :
            {
                if (!$this->get_component()->get_tool_browser()->get_parent()->isEmptyLearningPath($publication))
                {
                    return $this->get_progress($publication);
                }
                else
                {
                    return Translation::get('EmptyLearningPath');
                }
            }
        }

        return parent::render_cell($column, $publication);
    }

    /**
     * **************************************************************************************************************
     * Helper Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the progress of a given publication
     *
     * @param mixed[] $publication
     *
     * @return string
     */
    public function get_progress($publication)
    {
        /** @var TrackingService $trackingService */
        $trackingService = $this->get_component()->get_tool_browser()->get_parent()
            ->createTrackingServiceForPublicationAndCourse(
                $publication[ContentObjectPublication::PROPERTY_ID],
                $publication[ContentObjectPublication::PROPERTY_COURSE_ID]
            );

        /** @var User $user */
        $user = $this->get_component()->get_tool_browser()->get_parent()->getUser();

        $learningPath = new LearningPath();
        $learningPath->setId($publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]);

        $progress = $trackingService->getLearningPathProgress(
            $learningPath, $user,
                $this->get_component()->get_tool_browser()->get_parent()->getCurrentTreeNodeForLearningPath($learningPath)
        );

        if (!is_null($progress))
        {
            $progressBarRenderer = new ProgressBarRenderer();
            $bar = $progressBarRenderer->render($progress);
        }

        $url = $this->get_component()->get_url(
            array(
                Manager::PARAM_ACTION => Manager::ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT,
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID],
                \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ACTION =>
                    \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_REPORTING
            )
        );

        return Text::create_link($url, $bar);
    }
}