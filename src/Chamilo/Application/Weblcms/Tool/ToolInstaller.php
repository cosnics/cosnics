<?php
namespace Chamilo\Application\Weblcms\Tool;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\CourseType\Storage\DataManager as CourseTypeDataManager;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSection;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSetting;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSettingDefaultValue;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTool;
use Chamilo\Configuration\Package\Action\Installer;
use Chamilo\Configuration\Package\Service\PackageFactory;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

/**
 * Abstract class to define the installation of a tool
 *
 * @package application\weblcms;
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class ToolInstaller extends Installer
{

    /**
     * **************************************************************************************************************
     * Caching variables *
     * **************************************************************************************************************
     */

    /**
     * The registration of the tool This registration is needed to register the settings
     *
     * @var ToolRegistration
     */
    private $tool_registration;

    /**
     * The static tool settings with their created setting object for faster retrieval
     *
     * @var CourseSetting[string]
     */
    private $static_tool_settings;

    /**
     * **************************************************************************************************************
     * Main functionality *
     * **************************************************************************************************************
     */

    /**
     * Installs the tool
     *
     * @return boolean
     */
    public function extra()
    {
        // Set time and memory limit very low because this could be a lengthy process
        ini_set('memory_limit', -1);
        set_time_limit(0);

        if (!$this->register_tool())
        {
            return false;
        }

        if (!CourseSettingsController::getInstance()->install_course_settings(
            $this, $this->tool_registration->get_id()
        ))
        {
            return false;
        }

        if (!$this->install_static_settings())
        {
            return false;
        }

        if (!$this->install_tool_for_existing_course_types())
        {
            return false;
        }

        if (!$this->install_tool_for_existing_courses())
        {
            return false;
        }

        return true;
    }

    /**
     * **************************************************************************************************************
     * Helper functionality *
     * **************************************************************************************************************
     */

    /**
     * Registers the tool in the tool table
     *
     * @return bool
     */
    private function register_tool()
    {
        $toolNamespace = static::CONTEXT;
        $tool_name = ClassnameUtilities::getInstance()->getPackageNameFromNamespace($toolNamespace);

        $tool_object = new CourseTool();

        $tool_object->set_name($tool_name);
        $tool_object->set_section_type($this->retrieve_course_section_type_from_package_info());
        $tool_object->setContext($toolNamespace);

        if ($tool_object->create())
        {
            $this->add_message(
                Installer::TYPE_NORMAL, Translation::get('RegisteredTool')
            );
            $this->tool_registration = $tool_object;

            return true;
        }
        else
        {
            return $this->failed(
                Installer::TYPE_NORMAL, Translation::get('CouldNotRegisterTool')
            );
        }
    }

    /**
     * Retrieves a course section type from the package info.
     * If no package info or course section definition is found
     * the default section "tool" is selected.
     *
     * @return int
     */
    private function retrieve_course_section_type_from_package_info()
    {
        $section_type = CourseSection::TYPE_TOOL;

        $container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
        $packageFactory = $container->get(PackageFactory::class);

        $packageInformation = $packageFactory->getPackage(static::CONTEXT);

        $extra = (array) $packageInformation->get_extra();

        if (isset($extra['course-section']))
        {
            $section_type = CourseSection::get_type_from_type_name($extra['course-section']);
        }

        return $section_type;
    }

    /**
     * Installs the settings that are required for each tool (active, visible)
     *
     * @return boolean
     */
    private function install_static_settings()
    {
        foreach (CourseSetting::get_static_tool_settings() as $static_tool_setting)
        {
            $course_setting = new CourseSetting();
            $course_setting->set_tool_id($this->tool_registration->get_id());
            $course_setting->set_name($static_tool_setting);
            $course_setting->set_global_setting(0);

            if (!$course_setting->create())
            {
                return $this->failed(
                    Installer::TYPE_NORMAL,
                    Translation::get('CouldNotInstallStaticSettings')
                );
            }

            $course_setting_default_value = new CourseSettingDefaultValue();
            $course_setting_default_value->set_course_setting_id($course_setting->get_id());
            $course_setting_default_value->set_value(1);

            if (!$course_setting_default_value->create())
            {
                return $this->failed(
                    Installer::TYPE_NORMAL,
                    Translation::get('CouldNotInstallStaticSettings')
                );
            }

            $this->static_tool_settings[$static_tool_setting] = $course_setting;
        }

        $this->add_message(
            Installer::TYPE_NORMAL, Translation::get('InstalledStaticSettings')
        );

        return true;
    }

    /**
     * Installs the tool in the existing course types Adds the static tool settings with a default disabled value
     *
     * @return bool
     */
    protected function install_tool_for_existing_course_types()
    {
        $course_types = CourseTypeDataManager::retrieves(CourseType::class, new DataClassRetrievesParameters());
        foreach($course_types as $course_type)
        {
            if (!$this->install_static_tool_setting_relations_for_object(
                $course_type, '\Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseTypeRelCourseSetting',
                'set_course_type_id'
            ))
            {
                return $this->failed(
                    Installer::TYPE_NORMAL,
                    Translation::get('CouldNotInstallStaticSettingsForExistingCourseTypes')
                );
            }
        }

        $this->add_message(
            Installer::TYPE_NORMAL,
            Translation::get('InstalledStaticSettingsForExistingCourseTypes')
        );

        return true;
    }

    /**
     * Installs the tool in the existing courses Adds the static tool settings with a default disabled value Adds a
     * rights location for the tool in each course
     *
     * @return bool
     */
    protected function install_tool_for_existing_courses()
    {
        $course_management_rights = CourseManagementRights::getInstance();

        $courses = CourseDataManager::retrieves(Course::class, new DataClassRetrievesParameters());
        foreach($courses as $course)
        {
            if (!$this->install_static_tool_setting_relations_for_object(
                $course, '\Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseRelCourseSetting', 'set_course_id'
            ))
            {
                return $this->failed(
                    Installer::TYPE_NORMAL,
                    Translation::get('CouldNotInstallStaticSettingsForExistingCourses')
                );
            }

            $course_subtree_root_location_id =
                $course_management_rights->get_courses_subtree_root_id($course->get_id());

            $this->add_message(
                Installer::TYPE_NORMAL,
                Translation::get('InstalledStaticSettingsForExistingCourses')
            );

            if (!$course_management_rights->create_location_in_courses_subtree(
                CourseManagementRights::TYPE_COURSE_MODULE, $this->tool_registration->get_id(),
                $course_subtree_root_location_id, $course->get_id()
            ))
            {
                return $this->failed(
                    Installer::TYPE_NORMAL,
                    Translation::get('CouldNotInstallRightsLocationForExistingCourseTypes')
                );
            }
        }

        $this->add_message(
            Installer::TYPE_NORMAL,
            Translation::get('InstalledRightsLocationForExistingCourses')
        );

        return true;
    }

    /**
     * Installs the static tool setting relation and values for a given object
     *
     * @param DataClass $object
     * @param string $course_setting_relation_class_name
     * @param string $set_object_function
     *
     * @return bool
     */
    protected function install_static_tool_setting_relations_for_object(
        $object, $course_setting_relation_class_name, $set_object_function = null
    )
    {
        foreach ($this->static_tool_settings as $static_tool_setting)
        {
            $course_setting_relation = new $course_setting_relation_class_name();
            $course_setting_relation->set_course_setting_id($static_tool_setting->get_id());

            call_user_func(array($course_setting_relation, $set_object_function), $object->get_id());

            $course_setting_relation->set_value(0);

            if (!$course_setting_relation->create())
            {
                return false;
            }
        }

        return true;
    }
}
