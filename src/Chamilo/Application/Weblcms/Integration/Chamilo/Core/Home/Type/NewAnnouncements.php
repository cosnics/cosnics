<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\NewBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: new_announcements.class.php 216 2009-11-13 14:08:06Z kariboe $
 * 
 * @package application.lib.weblcms.block
 */

/**
 * This class represents a calendar repo_viewer component which can be used to browse through the possible learning
 * objects to publish.
 */
class NewAnnouncements extends NewBlock
{
    // const TOOL_ANNOUNCEMENT = 'announcement';
    public function display_content()
    {
        $publications = $this->get_content(self :: TOOL_ANNOUNCEMENT);
        
        if ($publications === self :: OVERSIZED_WARNING)
        {
            return $this->get_oversized_warning();
        }
        
        ksort($publications);
        $icon = $this->get_new_announcements_icon();
        
        $html = array();
        $html[] = '<ul style="padding: 0px; margin: 0px 0px 0px 15px;">';
        
        $current_course_id = - 1;
        foreach ($publications as $publication)
        {
            $course_id = $publication[ContentObjectPublication :: PROPERTY_COURSE_ID];
            $title = $publication[ContentObject :: PROPERTY_TITLE];
            
            if ($course_id != $current_course_id)
            {
                if ($current_course_id != - 1)
                {
                    $html[] = '</ul>';
                }
                $current_course_id = $course_id;
                $html[] = '<li>' . $this->get_course_by_id($current_course_id)->get_title() . '</li>';
                $html[] = '<ul style="padding: 0px; margin: 2px 2px 2px 20px;">';
            }
            $title = htmlspecialchars($title);
            $link = $this->get_course_viewer_link($this->get_course_by_id($course_id), $publication);
            $html[] = '<li style="list-style: none; list-style-image: url(' . $icon . ');">' . '<a href="' . $link .
                 '" >' . $title . '</a></li>';
        }
        if ($current_course_id != - 1)
        {
            $html[] = '</ul>';
        }
        $html[] = '</ul>';
        
        if (count($html) < 3)
        {
            return Translation :: get('NoNewAnnouncementsSinceLastVisit');
        }
        
        return implode(PHP_EOL, $html);
    }

    private function get_new_announcements_icon()
    {
        return Theme :: getInstance()->getImagePath(
            \Chamilo\Application\Weblcms\Tool\Manager :: get_tool_type_namespace(self :: TOOL_ANNOUNCEMENT)) . 'Logo/' .
             Theme :: ICON_MINI . '_new.png';
    }

    private function get_course_viewer_link($course, $publication)
    {
        $id = $publication[ContentObjectPublication :: PROPERTY_ID];
        
        $params[\Chamilo\Application\Weblcms\Manager :: PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager :: context();
        $params[\Chamilo\Application\Weblcms\Manager :: PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager :: ACTION_VIEW_COURSE;
        $params[\Chamilo\Application\Weblcms\Manager :: PARAM_COURSE] = $course->get_id();
        $params[\Chamilo\Application\Weblcms\Manager :: PARAM_TOOL] = self :: TOOL_ANNOUNCEMENT;
        $params[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION] = \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_VIEW;
        $params[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID] = $id;
        return Redirect :: get_link($params);
    }
}
