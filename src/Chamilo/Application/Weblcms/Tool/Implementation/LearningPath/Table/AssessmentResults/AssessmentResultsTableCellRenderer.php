<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Table\AssessmentResults;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Component\StatisticsViewerComponent;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 * Renders the cells of the table
 *
 * @author Bert De Clercq (Hogeschool Gent)
 */
class AssessmentResultsTableCellRenderer extends DataClassTableCellRenderer implements
    TableCellRendererActionsColumnSupport
{

    /**
     * Renders the cells with data from the assessment attempts.
     *
     * @param type $column
     * @param type $assessment_attempt
     *
     * @return string
     */
    public function render_cell($column, $assessment_attempt)
    {
        switch ($column->get_name())
        {
            case Translation :: get('User') :
                return \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                    \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                    $this->get_component()->get_user_id())->get_fullname();
            case Translation :: get('Date') :
                return DatetimeUtilities :: format_locale_date(null, $assessment_attempt->get_start_time());
            case Translation :: get('Score') :
                return $assessment_attempt->get_score() . '%';
            case Translation :: get('Time') :
                return DatetimeUtilities :: format_seconds_to_hours($assessment_attempt->get_total_time());
        }

        return parent :: render_cell($column, $assessment_attempt);
    }

    /**
     * Returns an action bar
     *
     * @param type $assessment_attempt
     *
     * @return Toolbar The toolbar with the actions.
     */
    public function get_actions($assessment_attempt)
    {
        $toolbar = new Toolbar();

        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('ViewResults'),
                Theme :: getInstance()->getCommonImagePath('Action/ViewResults'),
                $this->get_component()->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => Manager :: ACTION_VIEW_ASSESSMENT_RESULTS,
                        \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $this->get_component()->get_publication_id(),
                        \Chamilo\Application\Weblcms\Manager :: PARAM_USERS => $this->get_component()->get_user_id(),
                        Manager :: PARAM_ATTEMPT_ID => $this->get_component()->get_attempt_id(),
                        \Chamilo\Core\Repository\Display\Manager :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_component()->get_ccoi_id(),
                        Manager :: PARAM_ASSESSMENT_ID => $this->get_component()->get_assessment_id(),
                        Manager :: PARAM_LEARNING_PATH_ITEM_ATTEMPT_ID => $assessment_attempt->get_id())),
                ToolbarItem :: DISPLAY_ICON));

        if ($this->get_component()->is_allowed(WeblcmsRights :: DELETE_RIGHT))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('DeleteResult'),
                    Theme :: getInstance()->getCommonImagePath('Action/Delete'),
                    $this->get_component()->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => Manager :: ACTION_VIEW_ASSESSMENT_RESULTS,
                            \Chamilo\Application\Weblcms\Manager :: PARAM_PUBLICATION => $this->get_component()->get_publication_id(),
                            \Chamilo\Application\Weblcms\Manager :: PARAM_USERS => $this->get_component()->get_user_id(),
                            Manager :: PARAM_ATTEMPT_ID => $this->get_component()->get_attempt_id(),
                            StatisticsViewerComponent :: PARAM_DELETE_ID => $assessment_attempt->get_id())),
                    ToolbarItem :: DISPLAY_ICON,
                    true));
        }

        return $toolbar->as_html();
    }
}
