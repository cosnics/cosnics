<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager;
use Chamilo\Core\Tracking\Storage\DataClass\Tracker;
use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;
use Exception;

/**
 * Tracks the visits of a user to a course
 *
 * @package application\weblcms\integration\core\tracking
 */
class CourseVisit extends Tracker
{
    use DependencyInjectionContainerTrait;

    public const CONTEXT = 'Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking';

    public const PROPERTY_CATEGORY_ID = 'category_id';
    public const PROPERTY_COURSE_ID = 'course_id';
    public const PROPERTY_FIRST_ACCESS_DATE = 'first_access_date';
    public const PROPERTY_LAST_ACCESS_DATE = 'last_access_date';
    public const PROPERTY_PUBLICATION_ID = 'publication_id';
    public const PROPERTY_TOOL_ID = 'tool_id';
    public const PROPERTY_TOTAL_NUMBER_OF_ACCESS = 'total_number_of_access';
    public const PROPERTY_TOTAL_TIME = 'total_time';
    public const PROPERTY_USER_ID = 'user_id';
    public const TYPE_LEAVE = 'leave_course';
    public const TYPE_VISIT = 'visit_course';

    /**
     * Runs this tracker
     *
     * @param array $parameters
     *
     * @return bool
     */
    public function run(array $parameters = [])
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
     * Returns the default property names of this dataclass
     *
     * @return \string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_USER_ID,
                self::PROPERTY_COURSE_ID,
                self::PROPERTY_TOOL_ID,
                self::PROPERTY_CATEGORY_ID,
                self::PROPERTY_PUBLICATION_ID,
                self::PROPERTY_TOTAL_NUMBER_OF_ACCESS,
                self::PROPERTY_FIRST_ACCESS_DATE,
                self::PROPERTY_LAST_ACCESS_DATE,
                self::PROPERTY_TOTAL_TIME
            ]
        );
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'tracking_weblcms_course_visit';
    }

    /**
     * Returns the category_id
     *
     * @return int
     */
    public function get_category_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_CATEGORY_ID);
    }

    /**
     * Returns the course_id
     *
     * @return int
     */
    public function get_course_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_COURSE_ID);
    }

    /**
     * **************************************************************************************************************
     * Public Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the first_access_date
     *
     * @return int
     */
    public function get_first_access_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_FIRST_ACCESS_DATE);
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the last_access_date
     *
     * @return int
     */
    public function get_last_access_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_LAST_ACCESS_DATE);
    }

    /**
     * Returns the publication_id
     *
     * @return int
     */
    public function get_publication_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_PUBLICATION_ID);
    }

    /**
     * Returns the tool_id
     *
     * @return int
     */
    public function get_tool_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_TOOL_ID);
    }

    /**
     * Returns the total_number_of_access
     *
     * @return int
     */
    public function get_total_number_of_access()
    {
        return $this->getDefaultProperty(self::PROPERTY_TOTAL_NUMBER_OF_ACCESS);
    }

    /**
     * Returns the total_time
     *
     * @return int
     */
    public function get_total_time()
    {
        return $this->getDefaultProperty(self::PROPERTY_TOTAL_TIME);
    }

    /**
     * Returns the user_id
     *
     * @return int
     */
    public function get_user_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

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
        if ($this->isIdentified())
        {
            return DataManager::retrieve_by_id(self::class, $this->get_id());
        }

        return DataManager::retrieve_course_visit_by_user_and_course_data(
            $this->get_user_id(), $this->get_course_id(), $this->get_tool_id(), $this->get_category_id(),
            $this->get_publication_id(), $useNullValues
        );
    }

    /**
     * Sets the category_id
     *
     * @param int $category_id
     */
    public function set_category_id($category_id)
    {
        $this->setDefaultProperty(self::PROPERTY_CATEGORY_ID, $category_id);
    }

    /**
     * Sets the course_id
     *
     * @param int $course_id
     */
    public function set_course_id($course_id)
    {
        $this->setDefaultProperty(self::PROPERTY_COURSE_ID, $course_id);
    }

    /**
     * Sets the first_access_date
     *
     * @param int $first_access_date
     */
    public function set_first_access_date($first_access_date)
    {
        $this->setDefaultProperty(self::PROPERTY_FIRST_ACCESS_DATE, $first_access_date);
    }

    /**
     * Sets the last_access_date
     *
     * @param int $last_access_date
     */
    public function set_last_access_date($last_access_date)
    {
        $this->setDefaultProperty(self::PROPERTY_LAST_ACCESS_DATE, $last_access_date);
    }

    /**
     * Sets the publication_id
     *
     * @param int $publication_id
     */
    public function set_publication_id($publication_id)
    {
        $this->setDefaultProperty(self::PROPERTY_PUBLICATION_ID, $publication_id);
    }

    /**
     * Sets the tool_id
     *
     * @param int $tool_id
     */
    public function set_tool_id($tool_id)
    {
        $this->setDefaultProperty(self::PROPERTY_TOOL_ID, $tool_id);
    }

    /**
     * Sets the total_number_of_access
     *
     * @param int $total_number_of_access
     */
    public function set_total_number_of_access($total_number_of_access)
    {
        $this->setDefaultProperty(self::PROPERTY_TOTAL_NUMBER_OF_ACCESS, $total_number_of_access);
    }

    /**
     * Sets the total_time
     *
     * @param int $total_time
     */
    public function set_total_time($total_time)
    {
        $this->setDefaultProperty(self::PROPERTY_TOTAL_TIME, $total_time);
    }

    /**
     * Sets the user_id
     *
     * @param int $user_id
     */
    public function set_user_id($user_id)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $user_id);
    }

    /**
     * Tracks a leave from a course
     *
     * @param CourseVisit $course_visit
     *
     * @return CourseVisit
     * @throws \Exception
     */
    public function track_leave($course_visit)
    {
        if (!$course_visit)
        {
            throw new Exception('The given course visit can not be empty when tracking the leave of a course');
        }

        $time_spend_on_course = time() - $course_visit->get_last_access_date();
        $course_visit->set_total_time($course_visit->get_total_time() + $time_spend_on_course);

        return $course_visit->update();
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
        $this->initializeContainer();

        if (!$course_visit)
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
            $this->getPageConfiguration()->addHtmlHeader($html_header_id);
        }

        return $success;
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
    public function validate_parameters(array $parameters = [])
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
}
