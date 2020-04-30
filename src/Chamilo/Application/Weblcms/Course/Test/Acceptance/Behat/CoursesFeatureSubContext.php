<?php
namespace Chamilo\Application\Weblcms\Course\Test\Acceptance\Behat;

use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseUserRelation;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Behat\Behat\Context\Context;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Exception;

/**
 * Weblcms application courses submanager subcontext
 * 
 * @author : Pieterjan Broekaert
 */
class CoursesFeatureSubContext implements Context
{

    private $course;

    public function __construct(array $parameters)
    {
    }

    /**
     * **************************************************************************************************************
     * Hooks Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Create some prerequisites before running a course scenario
     * @BeforeScenario @courses
     */
    public function before_courses_scenario_hook($event)
    {
        $this->course = self::create_course();
        self::subscribe_admin($this->course);
        self::subscribe_student($this->course);
        self::subscribe_teacher($this->course);
    }

    /**
     * Cleanup after the courses scenarios
     * @AfterScenario @courses
     */
    public function after_courses_scenario_hook($event)
    {
        $this->course->delete();
    }

    /**
     * **************************************************************************************************************
     * Prerequisites Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Creates a new course
     * 
     * @return \application\weblcms\course\Course
     */
    protected function create_course()
    {
        $course = new Course();
        
        $course->set_title('Testcourse 1');
        $course->set_titular_id(2); // User admin
        $course->set_visual_code('TESTCOURSE1');
        $course->set_course_type_id(0);
        
        $course->create();
        $setting_values = array();
        
        $setting_values[CourseSettingsController::SETTING_PARAM_COURSE_SETTINGS] = array();
        
        $setting_values[CourseSettingsController::SETTING_PARAM_COURSE_SETTINGS][CourseSettingsConnector::CATEGORY] = $course->get_category_id();
        $setting_values[CourseSettingsController::SETTING_PARAM_COURSE_SETTINGS][CourseSettingsConnector::LANGUAGE] = $course->get_language();
        $setting_values[CourseSettingsController::SETTING_PARAM_COURSE_SETTINGS][CourseSettingsConnector::TITULAR] = $course->get_titular_id();
        
        $course->create_course_settings_from_values($setting_values, true);
        
        CourseManagementRights::getInstance()->create_rights_from_values($course, array());
        
        return $course;
    }

    /**
     * Subscribes the admin to the course
     * 
     * @param $course
     * @return bool
     */
    protected function subscribe_admin($course)
    {
        return $this->subscribe_user_by_username('admin', $course, CourseUserRelation::STATUS_TEACHER);
    }

    /**
     * Subscribes the student to the course
     * 
     * @param $course
     * @return bool
     */
    protected function subscribe_student($course)
    {
        return $this->subscribe_user_by_username('student', $course, CourseUserRelation::STATUS_STUDENT);
    }

    /**
     * Subscribes the teacher to the course
     * 
     * @param $course
     * @return bool
     */
    protected function subscribe_teacher($course)
    {
        return $this->subscribe_user_by_username('teacher', $course, CourseUserRelation::STATUS_TEACHER);
    }

    /**
     * Subscribes a user by a given username
     * 
     * @param string $username
     * @param \application\weblcms\course\Course $course
     * @param int $status
     *
     * @return bool
     */
    protected function subscribe_user_by_username($username, $course, $status)
    {
        $user = \Chamilo\Core\User\Storage\DataManager::retrieve_user_by_username($username);
        if (! $user)
        {
            return false;
        }
        
        return DataManager::subscribe_user_to_course(
            $course->get_id(), 
            $status, 
            $user->get_id());
    }

    /**
     * **************************************************************************************************************
     * Steps Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * @When /^I go to the course "([^"]*)"$/
     */
    public function iGoToTheCourse($course_title)
    {
        $course_url = $this->get_course_viewer_url_from_course_title($course_title);
        $this->getMainContext()->visit($course_url);
    }

    /**
     * @Given /^I go to the tool "([^"]*)" in the course "([^"]*)"$/
     */
    public function iGoToTheToolInTheCourse($tool_title, $course_title)
    {
        $this->getMainContext()->getSession()->visit(
            $this->get_tool_viewer_url_from_course_and_tool_title($course_title, $tool_title));
        // $this->getMainContext()->waitForFooter();
    }

    /**
     * **************************************************************************************************************
     * Steps Helper Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the url for the course viewer for a given course by course title
     * 
     * @param string $course_title
     *
     * @return string
     *
     * @throws \Exception
     */
    protected function get_course_viewer_url_from_course_title($course_title)
    {
        return $this->getMainContext()->getMinkParameter('base_url') .
             'index.php?application=application%5Cweblcms&go=course_viewer&course=' .
             $this->get_course_by_title($course_title)->get_id();
    }

    /**
     * Retrieves a course by a given title
     * 
     * @param string $course_title
     *
     * @return Course
     *
     * @throws \Exception
     */
    protected function get_course_by_title($course_title)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Course::class, Course::PROPERTY_TITLE),
            new StaticConditionVariable($course_title));
        
        $course = DataManager::retrieve(
            Course::class,
            new DataClassRetrieveParameters($condition));
        
        if (! $course)
        {
            throw new Exception('Could not find course with title ' . $course_title);
        }
        
        return $course;
    }

    /**
     * Returns the url to a tool by a given course title and tool title
     * 
     * @param string $course_title
     * @param string $tool_title
     *
     * @return string
     */
    protected function get_tool_viewer_url_from_course_and_tool_title($course_title, $tool_title)
    {
        return $this->get_course_viewer_url_from_course_title($course_title) . '&tool=' .
             (string) StringUtilities::getInstance()->createString($tool_title)->underscored();
    }
}