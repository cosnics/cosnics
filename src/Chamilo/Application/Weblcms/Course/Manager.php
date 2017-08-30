<?php
namespace Chamilo\Application\Weblcms\Course;

use Chamilo\Application\Weblcms\Course\Interfaces\CourseSubManagerSupport;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * This class describes the submanager for course management
 * 
 * @package \application\weblcms\course
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends Application
{
    /**
     * **************************************************************************************************************
     * PARAMATERS *
     * **************************************************************************************************************
     */
    const PARAM_ACTION = 'course_action';
    const PARAM_COURSE_ID = 'course_id';
    /**
     * **************************************************************************************************************
     * ACTIONS *
     * **************************************************************************************************************
     */
    const ACTION_BROWSE = 'Browse';
    const ACTION_BROWSE_UNSUBSCRIBED_COURSES = 'BrowseUnsubscribedCourses';
    const ACTION_BROWSE_SUBSCRIBED_COURSES = 'BrowseSubscribedCourses';
    const ACTION_CREATE = 'Create';
    const ACTION_DELETE = 'Delete';
    const ACTION_QUICK_CREATE = 'QuickCreate';
    const ACTION_QUICK_UPDATE = 'QuickUpdate';
    const ACTION_SUBSCRIBE = 'Subscribe';
    const ACTION_UNSUBSCRIBE = 'Unsubscribe';
    const ACTION_UPDATE = 'Update';
    const DEFAULT_ACTION = self::ACTION_BROWSE;

    /**
     * **************************************************************************************************************
     * Constructor Functionality *
     * **************************************************************************************************************
     */
    /**
     *
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Libraries\Architecture\Application\Application $parent
     * @throws \Exception
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        if (! $applicationConfiguration->getApplication() instanceof CourseSubManagerSupport)
        {
            throw new \Exception(
                'Components that use the course submanager support need to implement the CourseSubManagerSupport');
        }
        
        parent::__construct($applicationConfiguration);
    }

    /**
     * **************************************************************************************************************
     * Common Functionality *
     * **************************************************************************************************************
     */
    /**
     * Retrieves the first selected course
     * 
     * @return Course
     */
    protected function get_selected_course()
    {
        return $this->get_selected_courses()->next_result();
    }

    /**
     * Retrieves the selected course Use this function if you want to retrieve the selected course as a resultset
     * 
     * @return \libraries\storage\ResultSet <Course>
     */
    protected function get_selected_courses()
    {
        $course_ids = $this->get_selected_course_ids();
        $condition = new InCondition(
            new PropertyConditionVariable(Course::class_name(), Course::PROPERTY_ID), 
            $course_ids);
        $result_set = DataManager::retrieves(Course::class_name(), new DataClassRetrievesParameters($condition));
        if ($result_set->size() == 0)
        {
            throw new ObjectNotExistException(Translation::get('Course'), $course_ids);
        }
        return $result_set;
    }

    /**
     * Returns the selected course ids as an array
     * 
     * @return string[]
     */
    protected function get_selected_course_ids()
    {
        $course_ids = $this->getRequest()->get(self::PARAM_COURSE_ID);
        
        if (! isset($course_ids))
        {
            throw new NoObjectSelectedException(Translation::get('Course'));
        }
        
        if (! is_array($course_ids))
        {
            $course_ids = array($course_ids);
        }
        
        return $course_ids;
    }

    /**
     * **************************************************************************************************************
     * URL Building *
     * **************************************************************************************************************
     */
    /**
     * Returns the url to the course browse component for the given course id
     * 
     * @return String
     */
    public function get_browse_course_url()
    {
        return $this->get_action_url(self::ACTION_BROWSE);
    }

    /**
     * Returns the url to the subscribed courses browse component for the given course id
     * 
     * @return String
     */
    public function get_browse_subscribed_courses_url()
    {
        return $this->get_action_url(self::ACTION_BROWSE_SUBSCRIBED_COURSES);
    }

    /**
     * Returns the url to the unsubscribed courses browse component for the given course id
     * 
     * @return String
     */
    public function get_browse_unsubscribed_courses_url()
    {
        return $this->get_action_url(self::ACTION_BROWSE_UNSUBSCRIBED_COURSES);
    }

    /**
     * Returns the url to the course create component for the given course id
     * 
     * @param $course_id int
     *
     * @return String
     */
    public function get_create_course_url()
    {
        return $this->get_url(array(self::PARAM_ACTION => self::ACTION_CREATE));
    }

    /**
     * Returns the url to the course delete component for the given course id
     * 
     * @param $course_id int
     *
     * @return String
     */
    public function get_delete_course_url($course_id)
    {
        return $this->get_course_url(self::ACTION_DELETE, $course_id);
    }

    /**
     * Returns the url to the course update component for the given course id
     * 
     * @param $course_id int
     *
     * @return String
     */
    public function get_update_course_url($course_id)
    {
        return $this->get_course_url(self::ACTION_UPDATE, $course_id);
    }

    /**
     * Returns the url to subscribe a user to a course
     * 
     * @param $course_id int
     *
     * @return string
     */
    public function get_subscribe_to_course_url($course_id)
    {
        return $this->get_course_url(self::ACTION_SUBSCRIBE, $course_id);
    }

    /**
     * Returns the url to unsubscribe a user from a course
     * 
     * @param $course_id int
     *
     * @return string
     */
    public function get_unsubscribe_from_course_url($course_id)
    {
        return $this->get_course_url(self::ACTION_UNSUBSCRIBE, $course_id);
    }

    /**
     * Builds the view course home url
     * 
     * @param int $course_id
     *
     * @return string
     */
    public function get_view_course_home_url($course_id)
    {
        return $this->get_url(
            array(
                \Chamilo\Application\Weblcms\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE, 
                \Chamilo\Application\Weblcms\Manager::PARAM_COURSE => $course_id), 
            array(self::PARAM_ACTION));
    }

    /**
     * **************************************************************************************************************
     * Helper Functionality *
     * **************************************************************************************************************
     */
    /**
     * Returns a url for an action based on 1 specific course
     * 
     * @param $action string
     * @param $course_id int
     * @param $parameters string[] - Optional parameters
     * @return string
     */
    protected function get_course_url($action, $course_id, $parameters = array())
    {
        $parameters[self::PARAM_COURSE_ID] = $course_id;
        return $this->get_action_url($action, $parameters);
    }

    /**
     * Returns a url for a given action
     * 
     * @param $action string
     * @param $parameters string[] - Optional parameters
     * @return string
     */
    protected function get_action_url($action, $parameters = array())
    {
        $parameters[self::PARAM_ACTION] = $action;
        return $this->get_url($parameters);
    }
}
