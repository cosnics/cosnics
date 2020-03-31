<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Table\AsessmentAttempt;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Component\AttemptResultViewerComponent;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Storage\DataClass\Publication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\Hotpotatoes\Storage\DataClass\Hotpotatoes;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 * This class is a cell renderer for the attempts of an assessment
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssessmentAttemptTableCellRenderer extends RecordTableCellRenderer
    implements TableCellRendererActionsColumnSupport
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the actions toolbar
     *
     * @param mixed $assessment_attempt
     *
     * @return String
     */
    public function get_actions($assessment_attempt)
    {
        $pub = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(), $assessment_attempt[AssessmentAttempt::PROPERTY_ASSESSMENT_ID]
        );

        $assessment_attempt_status = $assessment_attempt[AssessmentAttempt::PROPERTY_STATUS];
        $assessment_attempt_id = $assessment_attempt[AssessmentAttempt::PROPERTY_ID];

        $assessment = $pub->get_content_object();

        $parameters = new DataClassRetrieveParameters(
            new EqualityCondition(
                new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_PUBLICATION_ID),
                new StaticConditionVariable($pub->get_id())
            )
        );
        $assessment_publication = DataManager::retrieve(Publication::class_name(), $parameters);

        $toolbar = new Toolbar();

        if ($assessment->get_type() != Hotpotatoes::class_name() &&
            (($assessment_attempt_status == AssessmentAttempt::STATUS_COMPLETED &&
                    $assessment_publication->get_configuration()->show_feedback()) ||
                $this->get_component()->is_allowed(WeblcmsRights::EDIT_RIGHT)))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('ViewResults'), new FontAwesomeGlyph('chart-line'),
                    $this->get_component()->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_ATTEMPT_RESULT_VIEWER,
                            Manager::PARAM_USER_ASSESSMENT => $assessment_attempt_id,
                            AttemptResultViewerComponent::PARAM_SHOW_FULL => 1
                        )
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('ViewResultsNA'), new FontAwesomeGlyph('chart-line', array('text-muted')), null,
                    ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($this->get_component()->is_allowed(WeblcmsRights::DELETE_RIGHT))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('DeleteResult'), new FontAwesomeGlyph('times'), $this->get_component()->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_DELETE_RESULTS,
                        Manager::PARAM_USER_ASSESSMENT => $assessment_attempt_id
                    )
                ), ToolbarItem::DISPLAY_ICON, true
                )
            );
        }

        return $toolbar->as_html();
    }

    /**
     * Renders a cell for a given object
     *
     * @param $column \libraries\ObjectTableColumn
     *
     * @param mixed $assessment_attempt
     *
     * @return String
     */
    public function render_cell($column, $assessment_attempt)
    {
        if ($column instanceof DataClassPropertyTableColumn)
        {
            switch ($column->get_class_name())
            {
                case AssessmentAttempt::class_name() :
                {
                    switch ($column->get_name())
                    {
                        case AssessmentAttempt::PROPERTY_START_TIME :
                            return DatetimeUtilities::format_locale_date(
                                null, $assessment_attempt[AssessmentAttempt::PROPERTY_START_TIME]
                            );
                        case AssessmentAttempt::PROPERTY_END_TIME :
                            if ($assessment_attempt[AssessmentAttempt::PROPERTY_END_TIME])
                            {
                                return DatetimeUtilities::format_locale_date(
                                    null, $assessment_attempt[AssessmentAttempt::PROPERTY_END_TIME]
                                );
                            }

                            return null;
                        case AssessmentAttempt::PROPERTY_TOTAL_TIME :
                            if ($assessment_attempt[AssessmentAttempt::PROPERTY_STATUS] ==
                                AssessmentAttempt::STATUS_COMPLETED)
                            {
                                return DatetimeUtilities::convert_seconds_to_hours(
                                    $assessment_attempt[AssessmentAttempt::PROPERTY_TOTAL_TIME]
                                );
                            }

                            return null;
                        case AssessmentAttempt::PROPERTY_TOTAL_SCORE :
                            if ($assessment_attempt[AssessmentAttempt::PROPERTY_STATUS] ==
                                AssessmentAttempt::STATUS_COMPLETED)
                            {
                                $total = $assessment_attempt[AssessmentAttempt::PROPERTY_TOTAL_SCORE];

                                return $total . '%';
                            }

                            return null;
                        case AssessmentAttempt::PROPERTY_STATUS :
                            return $assessment_attempt[AssessmentAttempt::PROPERTY_STATUS] ==
                            AssessmentAttempt::STATUS_COMPLETED ? Translation::get(
                                'Completed', null, 'Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking'
                            ) : Translation::get(
                                'NotCompleted', null, 'Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking'
                            );
                    }
                }
            }
        }

        return parent::render_cell($column, $assessment_attempt);
    }
}
