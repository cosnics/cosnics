<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\LearningPath;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\ToolBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\LearningPathAttemptProgressTemplate;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Component\StatisticsViewerComponent;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\String\Text;
use Chamilo\Libraries\Utilities\Utilities;

class LearningPathAttemptsBlock extends ToolBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data->set_rows(
            array(Translation :: get('Name'), Translation :: get('Progress'), Translation :: get('Details')));

        $pid = $this->get_parent()->get_publication_id();
        $course_id = $this->get_parent()->get_parent()->get_parent()->get_parameter(
            \Chamilo\Application\Weblcms\Manager :: PARAM_COURSE);
        $tool = $this->get_tool();

        $users_resultset = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_publication_target_users(
            $pid,
            $course_id);

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathAttempt :: class_name(),
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathAttempt :: PROPERTY_COURSE_ID),
            new StaticConditionVariable($course_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathAttempt :: class_name(),
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathAttempt :: PROPERTY_LEARNING_PATH_ID),
            new StaticConditionVariable($pid));
        $condition = new AndCondition($conditions);

        $attempts = \Chamilo\Libraries\Storage\DataManager\DataManager :: retrieves(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathAttempt :: class_name(),
            new DataClassRetrievesParameters($condition));

        while ($attempt = $attempts->next_result())
        {
            $user_progress[$attempt->get_user_id()] = $attempt;
        }

        $count = 1;
        $params = $this->get_parent()->get_parameters();
        $params[\Chamilo\Application\Weblcms\Manager :: PARAM_TEMPLATE_ID] = LearningPathAttemptProgressTemplate :: class_name();
        
        $img = '<img src="' . Theme :: getInstance()->getCommonImagePath('Action/Reporting') . '" title="' .
             Translation :: get('Details') . '" />';

        while ($user = $users_resultset->next_result())
        {
            $reporting_data->add_category($count);
            $reporting_data->add_data_category_row($count, Translation :: get('Name'), $user->get_fullname());

            $tracker = $user_progress[$user->get_id()];

            if ($tracker)
            {
                $params[\Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager :: PARAM_ATTEMPT_ID] = $tracker->get_id();
                $params[\Chamilo\Application\Weblcms\Manager :: PARAM_USERS] = $user->get_id();
                $params[\Chamilo\Application\Weblcms\Manager :: PARAM_PUBLICATION] = $pid;
                $params[StatisticsViewerComponent :: PARAM_STAT] = null;

                $redirect = new Redirect($params);
                $url = $redirect->getUrl();

                $params[StatisticsViewerComponent :: PARAM_STAT] = StatisticsViewerComponent :: ACTION_DELETE_LP_ATTEMPT;

                $redirect = new Redirect($params);
                $delete_url = $redirect->getUrl();

                if ($tool == 'reporting')
                {
                    $action = '<a href="' . $url . '">' . $img . '</a>';
                }
                else
                {
                    $action = Text :: create_link(
                        $url,
                        Theme :: getInstance()->getCommonImage(
                            'Action/Reporting',
                            'png',
                            Translation :: get('Details'),
                            null,
                            ToolbarItem :: DISPLAY_ICON));

                    $action .= ' ' . Text :: create_link(
                        $delete_url,
                        Theme :: getInstance()->getCommonImage(
                            'Action/Delete',
                            'png',
                            Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
                            null,
                            ToolbarItem :: DISPLAY_ICON));
                }

                $progress = $this->get_progress_bar($tracker->get_progress());

                $reporting_data->add_data_category_row($count, Translation :: get('Progress'), $progress);
                $reporting_data->add_data_category_row($count, Translation :: get('Details'), $action);
            }
            $count ++;
        }

        $reporting_data->hide_categories();
        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(\Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_TABLE);
    }
}
