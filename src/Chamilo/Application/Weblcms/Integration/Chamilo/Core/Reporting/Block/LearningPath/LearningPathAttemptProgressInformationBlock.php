<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\LearningPath;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\ToolBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathAttempt;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block displaying information about the assessment and
 *          access details
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class LearningPathAttemptProgressInformationBlock extends ToolBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data->set_categories(
            array(
                Translation::get('Title'), 
                Translation::get('Course'), 
                Translation::get('User'), 
                Translation::get('Progress')));
        
        $publication_id = $this->get_publication_id();
        $course_id = $this->get_course_id();
        $user_id = $this->get_user_id();
        
        $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
            \Chamilo\Core\User\Storage\DataClass\User::class_name(), 
            (int) $user_id);
        
        $publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(), 
            $publication_id);
        
        $course = CourseDataManager::retrieve_by_id(Course::class_name(), $course_id);
        
        $assessment = $publication->get_content_object();
        
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathAttempt::class_name(), 
                LearningPathAttempt::PROPERTY_LEARNING_PATH_ID), 
            new StaticConditionVariable($publication_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(LearningPathAttempt::class_name(), LearningPathAttempt::PROPERTY_USER_ID), 
            new StaticConditionVariable($user_id));
        $condition = new AndCondition($conditions);
        
        $attempt = \Chamilo\Libraries\Storage\DataManager\DataManager::retrieve(
            LearningPathAttempt::class_name(), 
            new DataClassRetrieveParameters($condition));
        
        $progress = $this->get_progress_bar($attempt->get_progress());
        
        $params = array();
        $params[Application::PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE;
        $params[Application::PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager::context();
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE] = $course_id;
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL] = ClassnameUtilities::getInstance()->getClassNameFromNamespace(
            LearningPath::class_name());
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION] = $publication_id;
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION] = \Chamilo\Application\Weblcms\Tool\Manager::ACTION_VIEW;
        
        $redirect = new Redirect($params);
        $url_title = $redirect->getUrl();
        
        $reporting_data->set_rows(array(Translation::get('Details')));
        $reporting_data->add_data_category_row(
            Translation::get('Title'), 
            Translation::get('Details'), 
            '<a href="' . $url_title . '">' . $assessment->get_title() . '</a>');
        $reporting_data->add_data_category_row(
            Translation::get('Course'), 
            Translation::get('Details'), 
            $course->get_title());
        $reporting_data->add_data_category_row(
            Translation::get('User'), 
            Translation::get('Details'), 
            $user->get_fullname());
        $reporting_data->add_data_category_row(Translation::get('Progress'), Translation::get('Details'), $progress);
        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(\Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_TABLE);
    }
}
