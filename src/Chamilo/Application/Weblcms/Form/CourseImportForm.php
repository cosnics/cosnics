<?php
namespace Chamilo\Application\Weblcms\Form;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseCategory;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Libraries\File\Import;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: course_import_form.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.course
 */
ini_set("max_execution_time", - 1);
ini_set("memory_limit", - 1);
class CourseImportForm extends FormValidator
{
    const TYPE_IMPORT = 1;

    private $failedcsv;

    public function __construct($form_type, $action)
    {
        parent :: __construct('course_import', 'post', $action);

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
        // $this->addElement('submit', 'course_import', Translation :: get('Ok',
        // null ,Utilities:: COMMON_LIBRARIES));
        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation :: get('Import', null, Utilities :: COMMON_LIBRARIES),
            null,
            null,
            'import');

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function import_courses()
    {
        $csvcourses = Import :: csv_to_array($_FILES['file']['tmp_name']);
        $failures = 0;

        foreach ($csvcourses as $csvcourse)
        {
            if (! $this->validate_data($csvcourse))
            {
                $failures ++;
                $this->failedcsv[] = Translation :: get('Invalid', null, Utilities :: COMMON_LIBRARIES) . ': ' .
                     implode($csvcourse, ';');
            }
        }

        if ($failures > 0)
        {
            return false;
        }

        foreach ($csvcourses as $csvcourse)
        {
            $teacher_info = $this->get_teacher_info($csvcourse['teacher']);

            $cat = DataManager :: retrieve_course_categories_ordered_by_name(
                new EqualityCondition(
                    new PropertyConditionVariable(CourseCategory :: class_name(), CourseCategory :: PROPERTY_NAME),
                    new StaticConditionVariable($csvcourse['category'])))->next_result();

            $catid = $cat ? $cat->get_id() : 0;
            $action = strtoupper($csvcourse['action']);

            // check course type
            $course_type_id = 0;
            $course_type_name = $csvcourse['course_type'];
            if ($course_type_name)
            {
                $course_type = \Chamilo\Application\Weblcms\CourseType\Storage\DataManager :: retrieve_course_type_by_name(
                    $course_type_name);
                if ($course_type)
                {
                    $course_type_id = $course_type->get_id();
                }
            }

            if ($action == 'A')
            {
                $course = new Course();
                $course->set_course_type_id($course_type_id);
                $course->set_visual_code($csvcourse['code']);
                $course->set_title($csvcourse[Course :: PROPERTY_TITLE]);

                // CTODO categories...
                $course->set_category_id($catid);

                $course->set_titular_id($teacher_info->get_id());
                $language = $csvcourse[Course :: PROPERTY_LANGUAGE];
                if (! $language)
                {
                    $language = $this->determine_default_course_language($course);
                }
                $course->set_language($language);

                if ($course->create())
                {
                    // create settings
                    $setting_values = array();
                    $setting_values[CourseSettingsController :: SETTING_PARAM_COURSE_SETTINGS][CourseSettingsConnector :: CATEGORY] = $catid;
                    $setting_values[CourseSettingsController :: SETTING_PARAM_COURSE_SETTINGS][CourseSettingsConnector :: LANGUAGE] = $language;
                    $setting_values[CourseSettingsController :: SETTING_PARAM_COURSE_SETTINGS][CourseSettingsConnector :: TITULAR] = $course->get_titular_id();

                    $course->create_course_settings_from_values($setting_values);
                    CourseManagementRights :: get_instance()->create_rights_from_values($course, array());

                    if (! \Chamilo\Application\Weblcms\Course\Storage\DataManager :: subscribe_user_to_course(
                        $course->get_id(),
                        '1',
                        $teacher_info->get_id()))
                    {
                        $failures ++;
                        $this->failedcsv[] = Translation :: get('SubscriptionFailed') . ':' . implode($csvcourse, ';');
                    }
                }
                else
                {
                    $failures ++;
                    $this->failedcsv[] = Translation :: get('CreationFailed') . ':' . implode($csvcourse, ';');
                }

                $setting_language = $course->get_course_setting(CourseSettingsConnector :: LANGUAGE);
                if ($setting_language != $course->get_language())
                {
                    $this->failedcsv[] = Translation :: get('LanguageSettingFailed') . ':' . implode($csvcourse, ';');
                    $course->set_language($setting_language);
                    if (! $course->update())
                    {
                        $this->failedcsv[] = Translation :: get('CreationFailed') . ':' . implode($csvcourse, ';');
                    }
                }
            }
            elseif ($action == 'U')
            {
                $course = CourseDataManager :: retrieve_course_by_visual_code($csvcourse['code']);

                $course->set_course_type_id($course_type_id);
                $course->set_title($csvcourse[Course :: PROPERTY_TITLE]);
                $language = $csvcourse[Course :: PROPERTY_LANGUAGE];
                if (! $language)
                {
                    $language = $this->determine_default_course_language($course);
                }
                $course->set_language($language);
                $course->set_category_id($catid);
                $course->set_titular_id($teacher_info->get_id());

                if ($course->update())
                {
                    $setting_values = array();
                    $setting_values[CourseSettingsController :: SETTING_PARAM_COURSE_SETTINGS][CourseSettingsConnector :: CATEGORY] = $catid;
                    $setting_values[CourseSettingsController :: SETTING_PARAM_COURSE_SETTINGS][CourseSettingsConnector :: LANGUAGE] = $language;
                    $setting_values[CourseSettingsController :: SETTING_PARAM_COURSE_SETTINGS][CourseSettingsConnector :: TITULAR] = $course->get_titular_id();
                    $course->update_course_settings_from_values($setting_values);
                }
                else
                {
                    $failures ++;
                    $this->failedcsv[] = Translation :: get('UpdateFailed') . ':' . implode($csvcourse, ';');
                }

                $setting_language = $course->get_course_setting(CourseSettingsConnector :: LANGUAGE);
                if ($setting_language != $course->get_language())
                {
                    $this->failedcsv[] = Translation :: get('LanguageSettingFailed') . ':' . implode($csvcourse, ';');
                    $course->set_language($setting_language);
                    if (! $course->update())
                    {
                        $this->failedcsv[] = Translation :: get('CreationFailed') . ':' . implode($csvcourse, ';');
                    }
                }
            }
            elseif ($action == 'D')
            {
                $course = CourseDataManager :: retrieve_course_by_visual_code($csvcourse['code']);

                if (! $course->delete())
                {
                    $failures ++;
                    $this->failedcsv[] = Translation :: get('DeleteFailed') . ':' . implode($csvcourse, ';');
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
    public function get_teacher_info($user_name)
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

        // 1. Action valid ?
        $action = strtoupper($csvcourse['action']);
        if ($action != 'A' && $action != 'D' && $action != 'U')
        {
            $failures ++;
        }

        // 2. check if code isn't in use for create and if code exists for
        // update / delete
        if (($action == 'A' && $this->is_course($csvcourse['code'])) ||
             ($action != 'A' && ! $this->is_course($csvcourse['code'])))
        {
            $failures ++;
        }

        if ($csvcourse['teacher'])
        {
            $csvcourse[Course :: PROPERTY_TITULAR_ID] = $csvcourse['teacher'];
        }

        // 3. check if teacher exists
        $teacher_info = $this->get_teacher_info($csvcourse[Course :: PROPERTY_TITULAR_ID]);
        if (! isset($teacher_info))
        {
            $failures ++;
        }

        // 4. check if category exists
        if (! $this->is_course_category($csvcourse['category']))
        {
            $failures ++;
        }

        // 5. check if the course_type exists (if the type is provided)
        $course_type = $csvcourse['course_type'];
        if ($course_type)
        {
            if (! $this->is_course_type($course_type))
            {
                $failures ++;
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

    private function is_course_type($type_name)
    {
        return \Chamilo\Application\Weblcms\CourseType\Storage\DataManager :: is_course_type_valid($type_name);
    }

    private function is_course_category($category_name)
    {
        $cat = DataManager :: retrieve_course_categories_ordered_by_name(
            new EqualityCondition(
                new PropertyConditionVariable(CourseCategory :: class_name(), CourseCategory :: PROPERTY_NAME),
                new StaticConditionVariable($category_name)))->next_result();

        if ($cat)
        {
            return true;
        }

        return false;
    }

    private function is_course($course_code)
    {
        $course = CourseDataManager :: retrieve_course_by_visual_code($course_code);

        return ! empty($course);
    }

    private function determine_default_course_language($course)
    {
        return CourseSettingsController :: get_instance()->get_course_type_setting(
            $course->get_course_type_id(),
            Course :: PROPERTY_LANGUAGE);
    }
}
