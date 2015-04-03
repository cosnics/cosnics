<?php
namespace Chamilo\Application\Weblcms\Renderer\ToolList\Type;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Renderer\ToolList\ToolListRenderer;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSection;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: shortcut_tool_list_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool_list_renderer
 */
/**
 * Tool list renderer to display a navigation menu.
 */
class ShortcutToolListRenderer extends ToolListRenderer
{

    // Inherited
    public function toHtml()
    {
        $this->is_course_admin = $this->get_parent()->get_parent()->is_teacher();
        return $this->show_tools($this->get_visible_tools());
    }

    /**
     * Show the tools of a given section
     *
     * @param $tools array
     */
    private function show_tools($tools)
    {
        $html = array();
        $parent = $this->get_parent();
        $course = $parent->get_course();

        foreach ($tools as $tool)
        {
            if ($tool->get_section_type() == CourseSection :: TYPE_ADMIN && ! $this->is_course_admin)
            {
                continue;
            }

            $new = '';
            if ($parent->tool_has_new_publications($tool->get_name()))
            {
                $new = '_new';
            }

            $tool_image = Theme :: ICON_MINI . $new;

            $title = htmlspecialchars(
                Translation :: get(
                    'TypeName',
                    null,
                    \Chamilo\Application\Weblcms\Tool\Manager :: get_tool_type_namespace($tool->get_name())));

            $params = array(
                Application :: PARAM_CONTEXT => Manager :: context(),
                Manager :: PARAM_COURSE => $course->get_id(),
                Application :: PARAM_ACTION => Manager :: ACTION_VIEW_COURSE,
                Manager :: PARAM_TOOL => $tool->get_name());

            $redirect = new Redirect($params, array(Manager :: PARAM_CATEGORY), true);
            $url = $redirect->getUrl();

            $html[] = '<a href="' . $url . '" title="' . $title . '">';
            $html[] = '<img src="' . Theme :: getInstance()->getImagePath(
                \Chamilo\Application\Weblcms\Tool\Manager :: get_tool_type_namespace($tool->get_name()),
                'Logo/' . $tool_image) . '" style="vertical-align: middle;" alt="' . $title . '"/> ';
            $html[] = '</a>';
        }

        return implode(PHP_EOL, $html);
    }
}
