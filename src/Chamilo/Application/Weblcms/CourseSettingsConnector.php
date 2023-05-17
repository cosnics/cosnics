<?php
namespace Chamilo\Application\Weblcms;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSetting;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Configuration\Configuration;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Format\Theme\ThemePathBuilder;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 * Simple connector class to facilitate rendering course settings
 *
 * @package application\weblcms;
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class CourseSettingsConnector
{
    public const ALLOW_FEEDBACK = 'allow_feedback';

    public const ALLOW_INTRODUCTION_TEXT = 'allow_introduction_text';

    public const BREADCRUMB_LAYOUT = 'breadcrumb_layout';

    public const BREADCRUMB_LAYOUT_COURSE_HOME = 3;

    public const BREADCRUMB_LAYOUT_TITLE = 1;

    public const BREADCRUMB_LAYOUT_VISUAL_CODE = 2;

    public const CATEGORY = 'category';

    public const COURSE_ACCESS = 'course_access';

    public const COURSE_ACCESS_CLOSED = 2;

    /**
     * **************************************************************************************************************
     * Definition of setting options *
     * **************************************************************************************************************
     */
    public const COURSE_ACCESS_OPEN = 1;

    public const LANGUAGE = 'language';

    public const OPEN_COURSE_ACCESS_PLATFORM = 2;

    public const OPEN_COURSE_ACCESS_REGISTERED_USERS = 1;

    public const OPEN_COURSE_ACCESS_TYPE = 'open_course_access_type';

    public const OPEN_COURSE_ACCESS_WORLD = 3;

    public const SHOW_COURSE_CODE = 'show_course_code';

    public const SHOW_COURSE_LANGUAGE = 'show_course_language';

    public const SHOW_COURSE_TITULAR = 'show_course_titular';

    public const THEME = 'theme';

    public const TITULAR = 'titular';

    public const TOOL_LAYOUT = 'tool_layout';

    public const TOOL_LAYOUT_THREE_COLUMNS = 2;

    public const TOOL_LAYOUT_THREE_COLUMNS_GROUP_INACTIVE = 4;

    public const TOOL_LAYOUT_TWO_COLUMNS = 1;

    public const TOOL_LAYOUT_TWO_COLUMNS_GROUP_INACTIVE = 3;

    public const TOOL_SHORTCUT_MENU = 'tool_shortcut_menu';

    /**
     * **************************************************************************************************************
     * Definition of settings *
     * **************************************************************************************************************
     */
    public const VISIBILITY = 'visibility';
    
    public static function getThemeSystemPathBuilder(): ThemePathBuilder
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            'Chamilo\Libraries\Format\Theme\ThemeSystemPathBuilder'
        );
    }

    /**
     * Returns the title for the course breadcrumb depending on the settings
     *
     * @param $course Course
     *
     * @return String
     */
    public static function get_breadcrumb_title_for_course(Course $course)
    {
        $course_settings_controller = CourseSettingsController::getInstance();
        $breadcrumb_setting = $course_settings_controller->get_course_setting($course, self::BREADCRUMB_LAYOUT);

        switch ($breadcrumb_setting)
        {
            case self::BREADCRUMB_LAYOUT_COURSE_HOME :
                return Translation::get('CourseHome');
            case self::BREADCRUMB_LAYOUT_VISUAL_CODE :
                return $course->get_visual_code();
            default :
                return $course->get_title();
        }
    }

    /**
     * Returns the available course categories
     *
     * @return string[]
     */
    public static function get_categories()
    {
        $categories = [];

        $categories_result_set = DataManager::retrieve_course_categories_ordered_by_name();
        foreach ($categories_result_set as $category)
        {
            $categories[$category->get_id()] = $category->get_name();
        }

        return $categories;
    }

    /**
     * Returns the settings that need to be copied to the course object
     *
     * @return string[]
     */
    public static function get_copied_settings_for_course()
    {
        return [
            self::LANGUAGE => Course::PROPERTY_LANGUAGE,
            self::CATEGORY => Course::PROPERTY_CATEGORY_ID,
            self::TITULAR => Course::PROPERTY_TITULAR_ID
        ];
    }

    /**
     * **************************************************************************************************************
     * Setting Helper Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the course property (if any) belonging to the given course setting
     *
     * @param $course_setting CourseSetting
     *
     * @return String | null
     */
    public static function get_course_property_for_setting(CourseSetting $course_setting)
    {
        $copied_settings = self::get_copied_settings_for_course();

        if (array_key_exists($course_setting->get_name(), $copied_settings))
        {
            return $copied_settings[$course_setting->get_name()];
        }
    }

    /**
     * **************************************************************************************************************
     * Special Settings Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the available platform languages
     *
     * @return string[]
     */
    public static function get_languages()
    {
        return array_merge(
            Configuration::getInstance()->getLanguages(),
            ['platform_language' => Translation::get('PlatformLanguage', null, 'Chamilo\Core\Admin')]
        );
    }

    /**
     * Returns the available themes
     *
     * @return string[]
     */
    public static function get_themes()
    {
        return self:: getThemeSystemPathBuilder()->getAvailableThemes();
    }

    /**
     * Returns the users with status teacher
     *
     * @return string[]
     */
    public static function get_titulars()
    {
        $users = [];
        $users[0] = Translation::get('TitularUnknown', null, 'Chamilo\Application\Weblcms\Course');

        $condition = new EqualityCondition(
            new PropertyConditionVariable(User::class, User::PROPERTY_STATUS),
            new StaticConditionVariable(User::STATUS_TEACHER)
        );

        $order = [
            new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME)),
            new OrderProperty(new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME))
        ];

        $format = Configuration::getInstance()->get_setting(['Chamilo\Core\User', 'fullname_format']);
        if ($format == User::NAME_FORMAT_LAST)
        {
            $order = array_reverse($order);
        }

        $users_result_set = \Chamilo\Core\User\Storage\DataManager::retrieve_active_users(
            $condition, null, null, new OrderBy($order)
        );

        foreach ($users_result_set as $user)
        {
            $users[$user->get_id()] = $user->get_fullname() . ' (' . $user->get_official_code() . ')';
        }

        return $users;
    }
}
