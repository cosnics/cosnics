<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Block;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\CourseVisit;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager as WeblcmsDataManager;
use Chamilo\Core\Repository\Common\Renderer\ContentObjectRenderer;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InequalityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * A notificationblock for new assignment submissions (assignmenttool)
 */
class AssignmentSubmissions extends Block
{

    public function displayContent()
    {
        $user_id = $this->getUserId();
        
        // Retrieve the assignments of the user
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication::class_name(), 
                \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication::PROPERTY_PUBLISHER_ID), 
            new StaticConditionVariable($user_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication::class_name(), 
                \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication::PROPERTY_TOOL), 
            new StaticConditionVariable('Assignment'));
        
        $condition = new AndCondition($conditions);
        
        $assignment_publications_resultset = WeblcmsDataManager::retrieves(
            ContentObjectPublication::class_name(), 
            new DataClassRetrievesParameters($condition));
        
        if ($assignment_publications_resultset->size() == 0)
        {
            return Translation::get('YouDoNotOwnAnyAssignments');
        }
        
        $items = array();
        
        while ($publication = $assignment_publications_resultset->next_result())
        {
            // Retrieve last time the publication was accessed
            $course_tool = WeblcmsDataManager::retrieve_course_tool_by_name($publication->get_tool());
            $last_access_time = $this->getLastVisit(
                $user_id, 
                $publication->get_course_id(), 
                $course_tool->get_id(), 
                $publication->get_category_id(), 
                $publication->get_id());
            
            $item = array();
            
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_PUBLICATION_ID), 
                new StaticConditionVariable($publication->get_id()));
            $conditions[] = new InequalityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_DATE_SUBMITTED), 
                InequalityCondition::GREATER_THAN, 
                new StaticConditionVariable($last_access_time));
            $condition = new AndCondition($conditions);
            
            $submissions_resultset = \Chamilo\Core\Tracking\Storage\DataManager::retrieves(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                new DataClassRetrievesParameters($condition));
            
            $object = $publication->get_content_object();
            $item[title] = $object->get_title();
            $parameters = array(
                Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::context(), 
                \Chamilo\Application\Weblcms\Manager::PARAM_COURSE => $publication->get_course_id(), 
                Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE, 
                \Chamilo\Application\Weblcms\Manager::PARAM_TOOL => 'Assignment', 
                \Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::ACTION_BROWSE_SUBMITTERS, 
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSER_TYPE => ContentObjectRenderer::TYPE_TABLE, 
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication->get_id());
            
            $redirect = new Redirect($parameters);
            
            $item[link] = $redirect->getUrl();
            
            $counter = $submissions_resultset->size();
            
            if ($counter > 0)
            {
                $item[count] = $counter;
                $items[] = $item;
            }
        }
        
        $html = $this->displayNewItems($items);
        
        if (count($html) == 0)
        {
            return Translation::get('NoNewSubmissionsSinceLastVisit');
        }
        
        return implode('', $html);
    }

    public function displayNewItems($items)
    {
        $html = array();
        
        foreach ($items as $item)
        {
            $html[] = '<a href="' . $item[link] . '">' . $item[title] . '</a>: ';
            $html[] = $item[count] . ' ' . Translation::get('New') . '<br />';
        }
        
        return $html;
    }

    private function getLastVisit($user_id, $course_id, $tool_id, $category_id, $publication_id)
    {
        $course_visit = new CourseVisit();
        $course_visit->set_user_id($user_id);
        $course_visit->set_course_id($course_id);
        $course_visit->set_tool_id($tool_id);
        
        if ($category_id == 0)
        {
            $category_id = null;
        }
        
        $course_visit->set_category_id($category_id);
        $course_visit->set_publication_id($publication_id);
        $course_visit = $course_visit->retrieve_course_visit_with_current_data();
        
        if (! $course_visit)
        {
            return 0;
        }
        
        return $course_visit->get_last_access_date();
    }
}
