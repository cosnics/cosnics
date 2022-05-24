<?php
namespace Chamilo\Application\Weblcms\CourseType;

use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\CourseType\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * This class describes the submanager for course type management
 * 
 * @package \application\weblcms\course_type
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends Application
{
    /**
     * **************************************************************************************************************
     * Parameters *
     * **************************************************************************************************************
     */
    const PARAM_ACTION = 'course_type_action';
    const PARAM_COURSE_TYPE_ID = 'course_type_id';
    const PARAM_MOVE_DIRECTION = 'direction';
    
    /**
     * **************************************************************************************************************
     * Actions *
     * **************************************************************************************************************
     */
    const ACTION_ACTIVATE = 'Activate';
    const ACTION_BROWSE = 'Browse';
    const ACTION_CHANGE_ACTIVATION = 'ChangeActivation';
    const ACTION_CREATE = 'Create';
    const ACTION_DEACTIVATE = 'Deactivate';
    const ACTION_DELETE = 'Delete';
    const ACTION_MOVE = 'Mover';
    const ACTION_UPDATE = 'Update';
    const ACTION_VIEW = 'View';
    const DEFAULT_ACTION = self::ACTION_BROWSE;
    
    /**
     * **************************************************************************************************************
     * Move Direction Definition *
     * **************************************************************************************************************
     */
    const MOVE_DIRECTION_UP = 1;
    const MOVE_DIRECTION_DOWN = 2;

    /**
     * **************************************************************************************************************
     * Common Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Retrieves the first selected course type
     * 
     * @return CourseType
     */
    protected function get_selected_course_type()
    {
        return $this->get_selected_course_types()->current();
    }

    /**
     * Retrieves the selected course types Use this function if you want to retrieve the selected course types as a
     * resultset
     * 
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassCollection<\Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType>
     */
    protected function get_selected_course_types()
    {
        $course_type_ids = $this->get_selected_course_type_ids();
        
        $condition = new InCondition(
            new PropertyConditionVariable(CourseType::class, CourseType::PROPERTY_ID),
            $course_type_ids);
        $result_set = DataManager::retrieves(CourseType::class, new DataClassRetrievesParameters($condition));
        
        if ($result_set->count() == 0)
        {
            throw new ObjectNotExistException(Translation::get('CourseType'), $course_type_ids);
        }
        
        return $result_set;
    }

    /**
     * Returns the selected course type ids as an array
     * 
     * @return string[]
     */
    protected function get_selected_course_type_ids()
    {
        $course_type_ids = $this->getRequest()->get(self::PARAM_COURSE_TYPE_ID);
        
        if (! isset($course_type_ids))
        {
            throw new NoObjectSelectedException(Translation::get('CourseType'));
        }
        
        if (! is_array($course_type_ids))
        {
            $course_type_ids = array($course_type_ids);
        }
        
        return $course_type_ids;
    }

    /**
     * **************************************************************************************************************
     * URL Building *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the url to the course type browse component for the given course type id
     * 
     * @param $course_type_id int
     *
     * @return String
     */
    public function get_browse_course_type_url()
    {
        return $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE));
    }

    /**
     * Returns the url to the course type create component for the given course type id
     * 
     * @param $course_type_id int
     *
     * @return String
     */
    public function get_create_course_type_url()
    {
        return $this->get_url(array(self::PARAM_ACTION => self::ACTION_CREATE));
    }

    /**
     * Returns the url to the course type viewing component for the given course type id
     * 
     * @param $course_type_id int
     *
     * @return String
     */
    public function get_view_course_type_url($course_type_id)
    {
        return $this->get_course_type_url(self::ACTION_VIEW, $course_type_id);
    }

    /**
     * Returns the url to the course type delete component for the given course type id
     * 
     * @param $course_type_id int
     *
     * @return String
     */
    public function get_delete_course_type_url($course_type_id)
    {
        return $this->get_course_type_url(self::ACTION_DELETE, $course_type_id);
    }

    /**
     * Returns the url to the course type update component for the given course type id
     * 
     * @param $course_type_id int
     *
     * @return String
     */
    public function get_update_course_type_url($course_type_id)
    {
        return $this->get_course_type_url(self::ACTION_UPDATE, $course_type_id);
    }

    /**
     * Returns the url to the course type change activation component for the given course type id
     * 
     * @param $course_type_id int
     *
     * @return String
     */
    public function get_change_course_type_activation_url($course_type_id)
    {
        return $this->get_course_type_url(self::ACTION_CHANGE_ACTIVATION, $course_type_id);
    }

    /**
     * Returns the url to the course type activate component for the given course type id
     * 
     * @param $course_type_id int
     *
     * @return String
     */
    public function get_activate_course_type_url($course_type_id)
    {
        return $this->get_course_type_url(self::ACTION_ACTIVATE, $course_type_id);
    }

    /**
     * Returns the url to the course type deactivate component for the given course type id
     * 
     * @param $course_type_id int
     *
     * @return String
     */
    public function get_deactivate_course_type_url($course_type_id)
    {
        return $this->get_course_type_url(self::ACTION_DEACTIVATE, $course_type_id);
    }

    /**
     * Returns the url to the course type mover component for the given course type id
     * 
     * @param $course_type_id int
     *
     * @return String
     */
    public function get_move_course_type_url($course_type_id, $direction = self::MOVE_DIRECTION_UP)
    {
        return $this->get_course_type_url(
            self::ACTION_MOVE, 
            $course_type_id, 
            array(self::PARAM_MOVE_DIRECTION => $direction));
    }

    /**
     * **************************************************************************************************************
     * Helper Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns a url for an action based on 1 specific course type
     * 
     * @param $action String
     * @param $course_type_id int
     * @param $parameters String[] - Optional parameters
     * @return String
     */
    private function get_course_type_url($action, $course_type_id, $parameters = [])
    {
        $parameters[self::PARAM_ACTION] = $action;
        $parameters[self::PARAM_COURSE_TYPE_ID] = $course_type_id;
        
        return $this->get_url($parameters);
    }
}
