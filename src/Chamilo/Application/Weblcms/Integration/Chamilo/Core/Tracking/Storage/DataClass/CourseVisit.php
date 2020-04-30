<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager;
use Chamilo\Core\Tracking\Storage\DataClass\Tracker;
use Chamilo\Libraries\Format\Structure\Page;
use Exception;

/**
 * Tracks the visits of a user to a course
 * 
 * @package application\weblcms\integration\core\tracking
 */
class CourseVisit extends Tracker
{
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_COURSE_ID = 'course_id';
    const PROPERTY_TOOL_ID = 'tool_id';
    const PROPERTY_CATEGORY_ID = 'category_id';
    const PROPERTY_PUBLICATION_ID = 'publication_id';
    const PROPERTY_FIRST_ACCESS_DATE = 'first_access_date';
    const PROPERTY_LAST_ACCESS_DATE = 'last_access_date';
    const PROPERTY_TOTAL_NUMBER_OF_ACCESS = 'total_number_of_access';
    const PROPERTY_TOTAL_TIME = 'total_time';
    const TYPE_VISIT = 'visit_course';
    const TYPE_LEAVE = 'leave_course';

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Runs this tracker
     * 
     * @param array $parameters
     *
     * @return bool
     */
    public function run(array $parameters = array())
    {
        $course_visit = $this->validate_parameters($parameters);
        
        switch ($this->get_event()->getType())
        {
            case self::TYPE_VISIT :
                return $this->track_visit($course_visit);
            case self::TYPE_LEAVE :
                return $this->track_leave($course_visit);
        }
        
        return false;
    }

    /**
     * Validates the given parameters and sets the parameters to this dataclass.
     * Retrieves and updates or creates a new
     * record.
     * 
     * @param array $parameters
     *
     * @return \application\weblcms\integration\core\tracking\CourseVisit
     */
    public function validate_parameters(array $parameters = array())
    {
        $this->set_id($parameters[self::PROPERTY_ID]);
        $this->set_user_id($parameters[self::PROPERTY_USER_ID]);
        $this->set_course_id($parameters[self::PROPERTY_COURSE_ID]);
        $this->set_tool_id($parameters[self::PROPERTY_TOOL_ID]);
        
        $categoryId = $parameters[self::PROPERTY_CATEGORY_ID] > 0 ? $parameters[self::PROPERTY_CATEGORY_ID] : null;
        
        $this->set_category_id($categoryId);
        
        $publication_id = $parameters[self::PROPERTY_PUBLICATION_ID];
        $publication_id = is_array($publication_id) ? $publication_id[0] : $publication_id;
        
        $this->set_publication_id($publication_id);
        
        return $this->retrieve_course_visit_with_current_data();
    }

    /**
     * Tracks a visit to a course
     * 
     * @param CourseVisit $course_visit
     *
     * @return CourseVisit
     */
    public function track_visit($course_visit)
    {
        if (! $course_visit)
        {
            $course_visit = $this;
            $course_visit->set_first_access_date(time());
        }
        
        $course_visit->set_total_number_of_access($course_visit->get_total_number_of_access() + 1);
        $course_visit->set_last_access_date(time());
        
        $success = $course_visit->save();
        
        if ($success)
        {
            $tracker_id = $course_visit->get_id();
            $html_header_id = "<script type=\"text/javascript\">var course_visit_tracker={$tracker_id};</script>";
            Page::getInstance()->getHeader()->addHtmlHeader($html_header_id);
        }
        
        return $success;
    }

    /**
     * Tracks a leave from a course
     * 
     * @param CourseVisit $course_visit
     *
     * @throws \Exception
     *
     * @return CourseVisit
     */
    public function track_leave($course_visit)
    {
        if (! $course_visit)
        {
            throw new Exception('The given course visit can not be empty when tracking the leave of a course');
        }
        
        $time_spend_on_course = time() - $course_visit->get_last_access_date();
        $course_visit->set_total_time($course_visit->get_total_time() + $time_spend_on_course);
        
        return $course_visit->update();
    }

    /**
     * Returns the default property names of this dataclass
     * 
     * @return \string[]
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_USER_ID, 
                self::PROPERTY_COURSE_ID, 
                self::PROPERTY_TOOL_ID, 
                self::PROPERTY_CATEGORY_ID, 
                self::PROPERTY_PUBLICATION_ID, 
                self::PROPERTY_TOTAL_NUMBER_OF_ACCESS, 
                self::PROPERTY_FIRST_ACCESS_DATE, 
                self::PROPERTY_LAST_ACCESS_DATE, 
                self::PROPERTY_TOTAL_TIME));
    }

    /**
     * **************************************************************************************************************
     * Public Functionality *
     * **************************************************************************************************************
     */

    /**
     * Retrieves an existing course visit record with the current data (by user_id, course_id, tool_id and
     * publication_id)
     *
     * @param bool $useNullValues
     *
     * @return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\CourseVisit|\Chamilo\Libraries\Storage\DataClass\DataClass
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function retrieve_course_visit_with_current_data($useNullValues = true)
    {
        // Retrieves the full class because the class can be identified without having all the necessary parameters
        // If the class is already fully retrieved from the database the caching system will catch this.
        if ($this->is_identified())
        {
            return DataManager::retrieve_by_id(self::class, $this->get_id());
        }
        
        return DataManager::retrieve_course_visit_by_user_and_course_data(
            $this->get_user_id(), 
            $this->get_course_id(), 
            $this->get_tool_id(), 
            $this->get_category_id(), 
            $this->get_publication_id(),
            $useNullValues
        );
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the user_id
     * 
     * @return int
     */
    public function get_user_id()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    /**
     * Sets the user_id
     * 
     * @param int $user_id
     */
    public function set_user_id($user_id)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $user_id);
    }

    /**
     * Returns the course_id
     * 
     * @return int
     */
    public function get_course_id()
    {
        return $this->get_default_property(self::PROPERTY_COURSE_ID);
    }

    /**
     * Sets the course_id
     * 
     * @param int $course_id
     */
    public function set_course_id($course_id)
    {
        $this->set_default_property(self::PROPERTY_COURSE_ID, $course_id);
    }

    /**
     * Returns the tool_id
     * 
     * @return int
     */
    public function get_tool_id()
    {
        return $this->get_default_property(self::PROPERTY_TOOL_ID);
    }

    /**
     * Sets the tool_id
     * 
     * @param int $tool_id
     */
    public function set_tool_id($tool_id)
    {
        $this->set_default_property(self::PROPERTY_TOOL_ID, $tool_id);
    }

    /**
     * Returns the category_id
     * 
     * @return int
     */
    public function get_category_id()
    {
        return $this->get_default_property(self::PROPERTY_CATEGORY_ID);
    }

    /**
     * Sets the category_id
     * 
     * @param int $category_id
     */
    public function set_category_id($category_id)
    {
        $this->set_default_property(self::PROPERTY_CATEGORY_ID, $category_id);
    }

    /**
     * Returns the publication_id
     * 
     * @return int
     */
    public function get_publication_id()
    {
        return $this->get_default_property(self::PROPERTY_PUBLICATION_ID);
    }

    /**
     * Sets the publication_id
     * 
     * @param int $publication_id
     */
    public function set_publication_id($publication_id)
    {
        $this->set_default_property(self::PROPERTY_PUBLICATION_ID, $publication_id);
    }

    /**
     * Returns the first_access_date
     * 
     * @return int
     */
    public function get_first_access_date()
    {
        return $this->get_default_property(self::PROPERTY_FIRST_ACCESS_DATE);
    }

    /**
     * Sets the first_access_date
     * 
     * @param int $first_access_date
     */
    public function set_first_access_date($first_access_date)
    {
        $this->set_default_property(self::PROPERTY_FIRST_ACCESS_DATE, $first_access_date);
    }

    /**
     * Returns the last_access_date
     * 
     * @return int
     */
    public function get_last_access_date()
    {
        return $this->get_default_property(self::PROPERTY_LAST_ACCESS_DATE);
    }

    /**
     * Sets the last_access_date
     * 
     * @param int $last_access_date
     */
    public function set_last_access_date($last_access_date)
    {
        $this->set_default_property(self::PROPERTY_LAST_ACCESS_DATE, $last_access_date);
    }

    /**
     * Returns the total_number_of_access
     * 
     * @return int
     */
    public function get_total_number_of_access()
    {
        return $this->get_default_property(self::PROPERTY_TOTAL_NUMBER_OF_ACCESS);
    }

    /**
     * Sets the total_number_of_access
     * 
     * @param int $total_number_of_access
     */
    public function set_total_number_of_access($total_number_of_access)
    {
        $this->set_default_property(self::PROPERTY_TOTAL_NUMBER_OF_ACCESS, $total_number_of_access);
    }

    /**
     * Returns the total_time
     * 
     * @return int
     */
    public function get_total_time()
    {
        return $this->get_default_property(self::PROPERTY_TOTAL_TIME);
    }

    /**
     * Sets the total_time
     * 
     * @param int $total_time
     */
    public function set_total_time($total_time)
    {
        $this->set_default_property(self::PROPERTY_TOTAL_TIME, $total_time);
    }
}
