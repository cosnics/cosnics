<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\LearningPath;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\ToolBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\LearningPathAttemptProgressTemplate;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathAttempt;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block with an overiew of the learning paths the user has
 *          attempted
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class CourseUserLearningPathInformationBlock extends ToolBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data->set_rows(
            array(
                Translation :: get('Title'),
                Translation :: get('Progress'),
                Translation :: get('LearningPathDetails')));
        $course_id = $this->get_parent()->get_parent()->get_parent()->get_parameter(
            \Chamilo\Application\Weblcms\Manager :: PARAM_COURSE);
        $user_id = $this->get_user_id();

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(LearningPathAttempt :: class_name(), LearningPathAttempt :: PROPERTY_COURSE_ID),
            new StaticConditionVariable($course_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(LearningPathAttempt :: class_name(), LearningPathAttempt :: PROPERTY_USER_ID),
            new StaticConditionVariable($user_id));
        $condition = new AndCondition($conditions);

        $attempts = \Chamilo\Libraries\Storage\DataManager\DataManager :: retrieves(
            LearningPathAttempt :: class_name(),
            new DataClassRetrievesParameters($condition));

        while ($attempt = $attempts->next_result())
        {
            $learning_paths[$attempt->get_learning_path_id()] = $attempt;
        }

        // TODO: Using the content object name for the tool name is a bad idea ... a better solution should be found and
        // implemented
        $toolName = ClassnameUtilities :: getInstance()->getClassNameFromNamespace(LearningPath :: class_name());

        $params = array();
        $params[Application :: PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager :: ACTION_VIEW_COURSE;
        $params[Application :: PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager :: context();
        $params[\Chamilo\Application\Weblcms\Manager :: PARAM_COURSE] = $course_id;
        $params[\Chamilo\Application\Weblcms\Manager :: PARAM_TOOL] = $toolName;
        $params[\Chamilo\Application\Weblcms\Manager :: PARAM_TOOL_ACTION] = \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_VIEW;

        $params_detail = $this->get_parent()->get_parameters();
        $params_detail[\Chamilo\Core\Reporting\Viewer\Manager :: PARAM_BLOCK_ID] = null;
        $params_detail[\Chamilo\Application\Weblcms\Manager :: PARAM_TEMPLATE_ID] = LearningPathAttemptProgressTemplate :: class_name();
        $img = '<img src="' . Theme :: getInstance()->getCommonImagePath('Action/Reporting') . '" title="' .
             Translation :: get('Details') . '" />';

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_TOOL),
            new StaticConditionVariable($toolName));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_COURSE_ID),
            new StaticConditionVariable($course_id));
        $condition = new AndCondition($conditions);
        $publications_resultset = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_content_object_publications(
            $condition);
        /*
         * $publications_arr = $publications_resultset->as_array();
         */
        $key = 0;
        while ($publication = $publications_resultset->next_result())
        {
            $progress = $url = $link = null;
            if (! \Chamilo\Application\Weblcms\Storage\DataManager :: is_publication_target_user(
                $user_id,
                $publication[ContentObjectPublication :: PROPERTY_ID]))
            {
                continue;
            }
            ++ $key;

            if ($learning_paths[$publication[ContentObjectPublication :: PROPERTY_ID]])
            {
                $params_detail[\Chamilo\Application\Weblcms\Manager :: PARAM_PUBLICATION] = $publication[ContentObjectPublication :: PROPERTY_ID];
                $params_detail[\Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager :: PARAM_ATTEMPT_ID] = $learning_paths[$publication[ContentObjectPublication :: PROPERTY_ID]];
                $link = '<a href="' . $this->get_parent()->get_url($params_detail) . '">' . $img . '</a>';
                $progress = $this->get_progress_bar(
                    $learning_paths[$publication[ContentObjectPublication :: PROPERTY_ID]]->get_progress());
            }

            $params[\Chamilo\Application\Weblcms\Manager :: PARAM_PUBLICATION] = $publication[ContentObjectPublication :: PROPERTY_ID];
            $learning_path = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                ContentObject :: class_name(),
                $publication[ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID]);

            $redirect = new Redirect($params);
            $url = '<a href="' . $redirect->getUrl() . '">' . $learning_path->get_title() . '</a>';

            $reporting_data->add_category($key);
            $reporting_data->add_data_category_row($key, Translation :: get('Title'), $url);
            $reporting_data->add_data_category_row($key, Translation :: get('Progress'), $progress);
            $reporting_data->add_data_category_row($key, Translation :: get('LearningPathDetails'), $link);
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
