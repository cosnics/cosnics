<?php
namespace Chamilo\Application\Weblcms\Interfaces;

/**
 * This interface determines that a form is supporting the course settings xml
 * form parser
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface CourseSettingsXmlFormParserSupport
{

    public function can_change_course_setting($name, $tool_id);
}
