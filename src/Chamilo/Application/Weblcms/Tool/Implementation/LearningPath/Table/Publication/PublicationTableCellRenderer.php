<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Table\Publication;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathAttempt;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Table\Publication\Table\ObjectPublicationTableCellRenderer;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Storage\DataManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
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
            case PublicationTableColumnModel :: COLUMN_PROGRESS :
            {
                if (!$this->get_component()->get_tool_browser()->get_parent()->is_empty_learning_path($publication))
                {
                    return $this->get_progress($publication);
                }
                else
                {
                    return Translation:: get('EmptyLearningPath');
                }
            }
        }

        return parent:: render_cell($column, $publication);
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
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathAttempt:: class_name(), LearningPathAttempt :: PROPERTY_COURSE_ID
            ),
            new StaticConditionVariable($this->get_component()->get_course_id())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathAttempt:: class_name(),
                LearningPathAttempt :: PROPERTY_LEARNING_PATH_ID
            ),
            new StaticConditionVariable($publication[ContentObjectPublication :: PROPERTY_ID])
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(LearningPathAttempt:: class_name(), LearningPathAttempt :: PROPERTY_USER_ID),
            new StaticConditionVariable($this->get_component()->get_user_id())
        );

        $condition = new AndCondition($conditions);

        $attempt = DataManager:: retrieve(
            LearningPathAttempt:: class_name(),
            new DataClassRetrieveParameters($condition)
        );

        if ($attempt instanceof LearningPathAttempt)
        {
            $progress = $attempt->get_progress();
        }
        else
        {
            $progress = 0;
        }

        $bar = $this->get_progress_bar($progress);
        $url = $this->get_component()->get_url(
            array(
                Manager :: PARAM_ACTION => Manager :: ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT,
                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $publication[ContentObjectPublication :: PROPERTY_ID],
                'lp_action' => 'view_progress'
            )
        );

        return Text:: create_link($url, $bar);
    }

    /**
     * Returns a progress bar
     *
     * @param number $progress
     *
     * @return string
     */
    private function get_progress_bar($progress)
    {
        $progress = round($progress);

        $html[] = '<div class="progress">';
        $html[] = '<div class="progress-bar" role="progressbar" aria-valuenow="' . $progress .
            '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $progress . '%;">';
        $html[] = $progress . '%';
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}