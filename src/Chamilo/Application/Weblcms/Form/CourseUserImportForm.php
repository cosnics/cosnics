<?php
namespace Chamilo\Application\Weblcms\Form;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseUserRelation;
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Libraries\File\Import;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: course_user_import_form.class.php 216 2009-11-13 14:08:06Z kariboe $
 * 
 * @package application.lib.weblcms.course
 */
ini_set("max_execution_time", - 1);
ini_set("memory_limit", - 1);
class CourseUserImportForm extends FormValidator
{
    const TYPE_IMPORT = 1;

    private $failedcsv;

    public function __construct($form_type, $action)
    {
        parent :: __construct('course_user_import', 'post', $action);
        
        $this->form_type = $form_type;
        $this->failedcsv = array();
        if ($this->form_type == self :: TYPE_IMPORT)
        {
            $this->build_importing_form();
        }
    }

    public function build_importing_form()
    {
        $this->addElement('file', 'file', Translation :: get('FileName'));
        // $this->addElement('submit', 'course_user_import', Translation ::
        // get('Ok'));
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation :: get('Ok', null, Utilities :: COMMON_LIBRARIES), 
            array('class' => 'positive'));
        // $buttons[] = $this->createElement('style_reset_button', 'reset',
        // Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function import_course_users()
    {
        $course = $this->course;
        
        $csvcourses = Import :: csv_to_array($_FILES['file']['tmp_name']);
        $failures = 0;
        
        foreach ($csvcourses as $csvcourse)
        {
            if (! $this->validate_data($csvcourse))
            {
                $failures ++;
                $this->failedcsv[] = Translation :: get('Invalid') . ': ' . implode($csvcourse, ';');
            }
        }
        
        if ($failures > 0)
        {
            return false;
        }
        
        foreach ($csvcourses as $csvcourse)
        {
            $user_info = $this->get_user_info($csvcourse['username']);
            
            $code = $csvcourse['coursecode'];
            $course = CourseDataManager :: retrieve_course_by_visual_code($code);
            
            $status = $csvcourse[CourseUserRelation :: PROPERTY_STATUS];
            $action = strtoupper($csvcourse['action']);
            
            if ($action == 'D' || $action == 'U')
            {
                if (! CourseDataManager :: unsubscribe_user_from_course($course->get_id(), $user_info->get_id()))
                {
                    $failures ++;
                    $this->failedcsv[] = Translation :: get('Failed', null, Utilities :: COMMON_LIBRARIES) . ': ' .
                         implode($csvcourse, ';');
                    continue;
                }
            }
            
            if ($action == 'A' || $action == 'U')
            {
                if (! CourseDataManager :: subscribe_user_to_course($course->get_id(), $status, $user_info->get_id()))
                {
                    $failures ++;
                    $this->failedcsv[] = Translation :: get('Failed', null, Utilities :: COMMON_LIBRARIES) . ': ' .
                         implode($csvcourse, ';');
                    continue;
                }
            }
        }
        
        if ($failures > 0)
        {
            return false;
        }
        else
        {
            return true;
        }
    }
    
    // TODO: Temporary solution pending implementation of user object
    public function get_user_info($user_name)
    {
        if (! \Chamilo\Core\User\Storage\DataManager :: is_username_available($user_name))
        {
            return \Chamilo\Core\User\Storage\DataManager :: retrieve_user_info($user_name);
        }
        else
        {
            return null;
        }
    }

    public function get_failed_csv()
    {
        return implode($this->failedcsv, '<br />');
    }

    public function validate_data($csvcourse)
    {
        $failures = 0;
        
        // 1. check if user exists
        // TODO: Change to appropriate property once the user-class is
        // operational
        $user_info = $this->get_user_info($csvcourse['username']);
        if (! isset($user_info))
        {
            $failures ++;
        }
        
        if ($csvcourse['coursecode'])
        {
            $csvcourse['course'] = $csvcourse['coursecode'];
        }
        
        // 2. check if course code exists
        if (! $this->is_course($csvcourse['course']))
        {
            $failures ++;
        }
        
        // 3. Status valid ?
        if ($csvcourse[CourseUserRelation :: PROPERTY_STATUS] != 1 &&
             $csvcourse[CourseUserRelation :: PROPERTY_STATUS] != 5)
        {
            $failures ++;
        }
        
        // 4. Action valid ?
        $action = strtoupper($csvcourse['action']);
        if ($action != 'A' && $action != 'D' && $action != 'U')
        {
            $failures ++;
        }
        
        if ($failures > 0)
        {
            return false;
        }
        else
        {
            return $csvcourse;
        }
    }

    public function is_course($course_code)
    {
        $course = CourseDataManager :: retrieve_course_by_visual_code($course_code);
        return ! is_null($course);
    }
}
