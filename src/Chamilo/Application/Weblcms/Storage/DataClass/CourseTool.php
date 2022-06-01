<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class defines a registered course tool on the platform.
 * The relation between a course (type) and a
 * course tool is defined through the course tool setting "active".
 *
 * @package application\weblcms\course;
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseTool extends DataClass
{
    const PROPERTY_CONTEXT = 'context';
    const PROPERTY_NAME = 'name';
    const PROPERTY_SECTION_TYPE = 'section_type';

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     *
     * @return string
     */
    function getContext()
    {
        return $this->getDefaultProperty(self::PROPERTY_CONTEXT);
    }

    /**
     * Returns the default properties of this dataclass
     *
     * @return String[] - The property names.
     */
    static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            array(self::PROPERTY_SECTION_TYPE, self::PROPERTY_NAME, self::PROPERTY_CONTEXT)
        );
    }

    /**
     * **************************************************************************************************************
     * Getters and Setters *
     * **************************************************************************************************************
     */

    /**
     * Returns the dependencies for this dataclass
     *
     * @return string[string]
     *
     */
    protected function getDependencies(array $dependencies = []): array
    {
        $id = $this->get_id();

        return array(
            CourseToolRelCourseSection::class => new EqualityCondition(
                new PropertyConditionVariable(
                    CourseToolRelCourseSection::class, CourseToolRelCourseSection::PROPERTY_TOOL_ID
                ), new StaticConditionVariable($id)
            )
        );
    }

    /**
     * Returns the name property of this object
     *
     * @return String
     */
    function get_name()
    {
        return $this->getDefaultProperty(self::PROPERTY_NAME);
    }

    /**
     * Returns the section_type property of this object
     *
     * @return String
     */
    function get_section_type()
    {
        return $this->getDefaultProperty(self::PROPERTY_SECTION_TYPE);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'weblcms_course_tool';
    }

    /**
     *
     * @param string $context
     */
    function setContext($context)
    {
        $this->setDefaultProperty(self::PROPERTY_CONTEXT, $context);
    }

    /**
     * Sets the name property of this object
     *
     * @param $name String
     */
    function set_name($name)
    {
        $this->setDefaultProperty(self::PROPERTY_NAME, $name);
    }

    /**
     * Sets the section_type property of this object
     *
     * @param $section_type String
     */
    function set_section_type($section_type)
    {
        $this->setDefaultProperty(self::PROPERTY_SECTION_TYPE, $section_type);
    }
}