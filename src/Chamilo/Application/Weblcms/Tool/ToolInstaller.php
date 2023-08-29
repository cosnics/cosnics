<?php
namespace Chamilo\Application\Weblcms\Tool;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\CourseType\Storage\DataManager as CourseTypeDataManager;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSection;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSetting;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSettingDefaultValue;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTool;
use Chamilo\Configuration\Package\Action;
use Chamilo\Configuration\Package\Action\Installer;
use Chamilo\Configuration\Package\Service\PackageFactory;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

/**
 * Abstract class to define the installation of a tool
 *
 * @package application\weblcms;
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class ToolInstaller extends Installer
{
    /**
     * The static tool settings with their created setting object for faster retrieval
     *
     * @var \Chamilo\Application\Weblcms\Storage\DataClass\CourseSetting[]
     */
    private array $static_tool_settings;

    /**
     * The registration of the tool This registration is needed to register the settings
     */
    private CourseTool $tool_registration;

    /**
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Exception
     */
    public function extra(array $formValues): bool
    {
        // Set time and memory limit very low because this could be a lengthy process
        ini_set('memory_limit', - 1);
        set_time_limit(0);

        if (!$this->register_tool())
        {
            return false;
        }

        if (!CourseSettingsController::getInstance()::install_course_settings(
            $this, $this->tool_registration->getId()
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
     * Installs the settings that are required for each tool (active, visible)
     *
     * @throws \ReflectionException
     */
    private function install_static_settings(): bool
    {
        $translator = $this->getTranslator();

        foreach (CourseSetting::get_static_tool_settings() as $static_tool_setting)
        {
            $course_setting = new CourseSetting();
            $course_setting->set_tool_id($this->tool_registration->getId());
            $course_setting->set_name($static_tool_setting);
            $course_setting->set_global_setting(0);

            if (!$course_setting->create())
            {
                return $this->failed(
                    $translator->trans('CouldNotInstallStaticSettings', [],
                        \Chamilo\Application\Weblcms\Manager::CONTEXT)
                );
            }

            $course_setting_default_value = new CourseSettingDefaultValue();
            $course_setting_default_value->set_course_setting_id($course_setting->getId());
            $course_setting_default_value->set_value(1);

            if (!$course_setting_default_value->create())
            {
                return $this->failed(
                    $translator->trans('CouldNotInstallStaticSettings', [],
                        \Chamilo\Application\Weblcms\Manager::CONTEXT)
                );
            }

            $this->static_tool_settings[$static_tool_setting] = $course_setting;
        }

        $this->add_message(
            Action::TYPE_NORMAL,
            $translator->trans('InstalledStaticSettings', [], \Chamilo\Application\Weblcms\Manager::CONTEXT)
        );

        return true;
    }

    /**
     * Installs the static tool setting relation and values for a given object
     */
    protected function install_static_tool_setting_relations_for_object(
        DataClass $object, string $course_setting_relation_class_name, ?string $set_object_function = null
    ): bool
    {
        foreach ($this->static_tool_settings as $static_tool_setting)
        {
            $course_setting_relation = new $course_setting_relation_class_name();
            $course_setting_relation->set_course_setting_id($static_tool_setting->getId());

            call_user_func([$course_setting_relation, $set_object_function], $object->getId());

            $course_setting_relation->set_value(0);

            if (!$course_setting_relation->create())
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Installs the tool in the existing course types Adds the static tool settings with a default disabled value
     *
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \ReflectionException
     */
    protected function install_tool_for_existing_course_types(): bool
    {
        $translator = $this->getTranslator();
        $course_types = CourseTypeDataManager::retrieves(CourseType::class, new DataClassRetrievesParameters());

        foreach ($course_types as $course_type)
        {
            if (!$this->install_static_tool_setting_relations_for_object(
                $course_type, '\Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseTypeRelCourseSetting',
                'set_course_type_id'
            ))
            {
                return $this->failed(
                    $translator->trans('CouldNotInstallStaticSettingsForExistingCourseTypes', [],
                        \Chamilo\Application\Weblcms\Manager::CONTEXT)
                );
            }
        }

        $this->add_message(
            Action::TYPE_NORMAL, $translator->trans('InstalledStaticSettingsForExistingCourseTypes', [],
            \Chamilo\Application\Weblcms\Manager::CONTEXT)
        );

        return true;
    }

    /**
     * Installs the tool in the existing courses Adds the static tool settings with a default disabled value Adds a
     * rights location for the tool in each course
     *
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function install_tool_for_existing_courses(): bool
    {
        $translator = $this->getTranslator();
        $course_management_rights = CourseManagementRights::getInstance();
        $courses = CourseDataManager::retrieves(Course::class, new DataClassRetrievesParameters());

        foreach ($courses as $course)
        {
            if (!$this->install_static_tool_setting_relations_for_object(
                $course, '\Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseRelCourseSetting', 'set_course_id'
            ))
            {
                return $this->failed(
                    $translator->trans('CouldNotInstallStaticSettingsForExistingCourses', [],
                        \Chamilo\Application\Weblcms\Manager::CONTEXT)
                );
            }

            $course_subtree_root_location_id = $course_management_rights->get_courses_subtree_root_id($course->getId());

            $this->add_message(
                Action::TYPE_NORMAL, $translator->trans('InstalledStaticSettingsForExistingCourses', [],
                \Chamilo\Application\Weblcms\Manager::CONTEXT)
            );

            if (!$course_management_rights->create_location_in_courses_subtree(
                WeblcmsRights::TYPE_COURSE_MODULE, $this->tool_registration->getId(), $course_subtree_root_location_id,
                $course->getId()
            ))
            {
                return $this->failed(
                    $translator->trans('CouldNotInstallRightsLocationForExistingCourseTypes', [],
                        \Chamilo\Application\Weblcms\Manager::CONTEXT)
                );
            }
        }

        $this->add_message(
            Action::TYPE_NORMAL, $translator->trans('InstalledRightsLocationForExistingCourses', [],
            \Chamilo\Application\Weblcms\Manager::CONTEXT)
        );

        return true;
    }

    /**
     * Registers the tool in the tool table
     *
     * @throws \Exception
     */
    private function register_tool(): bool
    {
        $translator = $this->getTranslator();

        $toolNamespace = $this->getContext();
        $tool_name = ClassnameUtilities::getInstance()->getPackageNameFromNamespace($toolNamespace);

        $tool_object = new CourseTool();

        $tool_object->set_name($tool_name);
        $tool_object->set_section_type($this->retrieve_course_section_type_from_package_info());
        $tool_object->setContext($toolNamespace);

        if ($tool_object->create())
        {
            $this->add_message(
                Action::TYPE_NORMAL, $translator->trans('RegisteredTool')
            );
            $this->tool_registration = $tool_object;

            return true;
        }
        else
        {
            return $this->failed(
                $translator->trans('CouldNotRegisterTool')
            );
        }
    }

    /**
     * Retrieves a course section type from the package info.
     * If no package info or course section definition is found
     * the default section "tool" is selected.
     *
     * @throws \Exception
     */
    private function retrieve_course_section_type_from_package_info(): int
    {
        $section_type = CourseSection::TYPE_TOOL;

        $container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
        $packageFactory = $container->get(PackageFactory::class);

        $packageInformation = $packageFactory->getPackage($this->getContext());

        $extra = (array) $packageInformation->get_extra();

        if (isset($extra['course-section']))
        {
            $section_type = CourseSection::get_type_from_type_name($extra['course-section']);
        }

        return $section_type;
    }
}
