<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\NewBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\Common\Renderer\ContentObjectRenderer;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * A notificationblock for new assignment submissions (assignmenttool)
 */
class NewAssignments extends NewBlock
{

    public function display_content()
    {
        $publications = $this->get_content(self :: TOOL_ASSIGNMENT);
        $html = $this->display_new_items($publications);
        
        if (count($html) < 3)
        {
            return Translation :: get('NoNewAssignmentsSinceLastVisit');
        }
        return implode(PHP_EOL, $html);
    }

    public function display_new_items($publications)
    {
        ksort($publications);
        $icon = '<img src="' . $this->get_new_assignments_icon() . '"/>';
        
        $html = array();
        $html[] = '<ul style="padding: 0px; margin: 0px 0px 0px 15px;">';
        $current_course_id = - 1;
        foreach ($publications as $publication)
        {
            $course_id = $publication[ContentObjectPublication :: PROPERTY_COURSE_ID];
            $id = $publication[ContentObjectPublication :: PROPERTY_ID];
            
            if ($publication[ContentObject :: PROPERTY_TYPE] != Assignment :: class_name())
                continue;
            
            $content_object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object(
                $publication[ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID], 
                Assignment :: class_name());
            
            if ($course_id != $current_course_id)
            {
                $current_course_id = $course_id;
                $html[] = '<li>' . $this->get_course_by_id($current_course_id)->get_title() . '</li>';
            }
            
            $parameters = array(
                \Chamilo\Application\Weblcms\Manager :: PARAM_COURSE => $course_id, 
                Application :: PARAM_ACTION => \Chamilo\Application\Weblcms\Manager :: ACTION_VIEW_COURSE, 
                Application :: PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager :: context(), 
                \Chamilo\Application\Weblcms\Manager :: PARAM_TOOL => NewBlock :: TOOL_ASSIGNMENT, 
                \Chamilo\Application\Weblcms\Manager :: PARAM_TOOL_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager :: ACTION_BROWSE_SUBMITTERS, 
                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_BROWSER_TYPE => ContentObjectRenderer :: TYPE_TABLE, 
                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $id);
            
            $link = Redirect :: get_link($parameters);
            
            $start_date = DatetimeUtilities :: format_locale_date(
                Translation :: get('DateFormatShort', null, Utilities :: COMMON_LIBRARIES), 
                $content_object->get_start_time());
            $end_date = DatetimeUtilities :: format_locale_date(
                Translation :: get('DateFormatShort', null, Utilities :: COMMON_LIBRARIES), 
                $content_object->get_end_time());
            $html[] = '<a href="' . $link . '">' . $icon . ' ' . $content_object->get_title() . '</a>: ' . Translation :: get(
                'From') . ' ' . $start_date . ' ' . Translation :: get('Until') . ' ' . $end_date . '<br />';
        }
        $html[] = '</ul>';
        return $html;
    }

    private function get_new_assignments_icon()
    {
        return Theme :: getInstance()->getImagePath(
            \Chamilo\Application\Weblcms\Tool\Manager :: get_tool_type_namespace(self :: TOOL_ASSIGNMENT)) . 'Logo/' .
             Theme :: ICON_MINI . '_new.png';
    }
}
