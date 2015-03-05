<?php
namespace Chamilo\Application\Weblcms\Renderer\ToolList\Type;

use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Renderer\ToolList\ToolListRenderer;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSection;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: menu_tool_list_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool_list_renderer
 */
/**
 * Tool list renderer to display a navigation menu.
 */
class MenuToolListRenderer extends ToolListRenderer
{

    private $is_course_admin;

    private $menu_properties;

    /**
     * Constructor
     *
     * @param $parent WebLcms The parent application
     */
    public function __construct($parent, $visible_tools)
    {
        parent :: __construct($parent, $visible_tools);
        $this->is_course_admin = $this->get_parent()->is_allowed(WeblcmsRights :: EDIT_RIGHT);
        $this->menu_properties = $this->load_menu_properties();
    }

    // Inherited
    public function toHtml()
    {
        return $this->show_tools($this->get_visible_tools());
    }

    /**
     * Show the tools of a given section
     *
     * @param $tools array
     */
    private function show_tools($tools)
    {
        $parent = $this->get_parent();
        $html = array();

        $menu_style = $this->get_menu_style();

        $html[] = '<div id="tool_bar" class="tool_bar tool_bar_' .
             ($this->display_menu_icons() && ! $this->display_menu_text() ? 'icon_' : '') . $menu_style . '">';

        if ($this->get_menu_style() == 'right')
        {
            $html[] = '<div id="tool_bar_hide_container" class="hide">';
            $html[] = '<a id="tool_bar_hide" href="#"><img src="' . Theme :: getInstance()->getCommonImagePath(
                'Action/ActionBar_' . $menu_style . '_hide') . '" /></a>';
            $html[] = '<a id="tool_bar_show" href="#"><img src="' . Theme :: getInstance()->getCommonImagePath(
                'Action/ActionBar_' . $menu_style . '_show') . '" /></a>';
            $html[] = '</div>';
        }

        $html[] = '<div class="tool_menu">';
        $html[] = '<ul>';

        $show_search = false;

        foreach ($tools as $tool)
        {
            if ($tool->get_section_type() == CourseSection :: TYPE_ADMIN)
            {
                $admin_tools[] = $tool;
                continue;
            }

            if ($tool->get_name() == 'search')
            {
                $show_search = true;
            }

            $html[] = $this->display_tool($tool);
        }

        if (count($admin_tools) && $this->is_course_admin)
        {
            $html[] = '<div style="margin: 10px 0 10px 0; border-bottom: 1px dotted #4271B5; height: 0px;"></div>';
            foreach ($admin_tools as $tool)
            {
                $html[] = $this->display_tool($tool);
            }
        }
        $html[] = '</ul>';

        if ($this->display_menu_text() && $show_search)
        {
            $html[] = '<div style="margin: 10px 0 10px 0; border-bottom:
                1px dotted #4271B5; height: 0px; text-align: center;"></div>';

            $form = new FormValidator(
                'search_simple',
                'post',
                $parent->get_url(
                    array(
                        Manager :: PARAM_TOOL => 'search',
                        \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Search\Manager :: ACTION_SEARCH)),
                '',
                array('style' => 'text-align: center;'),
                false);

            $renderer = clone $form->defaultRenderer();
            $renderer->setFormTemplate('<form {attributes}>{content}</form>');
            $renderer->setElementTemplate('{element}<br />');

            $form->addElement(
                'text',
                'query',
                '',
                'size="18" class="search_query_no_icon" style="background-color: white;
                border: 1px solid grey; height: 18px; margin-bottom: 10px;"');
            $form->addElement(
                'style_submit_button',
                'submit',
                Translation :: get('Search', null, Utilities :: COMMON_LIBRARIES),
                array('class' => 'normal search'));
            $form->accept($renderer);

            $html[] = $renderer->toHtml();
        }

        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp;</div>';

        if ($this->get_menu_style() == 'left')
        {
            $html[] = '<div id="tool_bar_hide_container" class="hide">';
            $html[] = '<a id="tool_bar_hide" href="#"><img src="' . Theme :: getInstance()->getCommonImagePath(
                'Action/Action_bar_' . $menu_style . '_hide') . '" /></a>';
            $html[] = '<a id="tool_bar_show" href="#"><img src="' . Theme :: getInstance()->getCommonImagePath(
                'Action/Action_bar_' . $menu_style . '_show') . '" /></a>';
            $html[] = '</div>';
        }

        $html[] = '</div>';
        $html[] = '<script type="text/javascript" src="' .
             Path :: getInstance()->namespaceToFullPath('Chamilo\Configuration', true) .
             'Resources/Javascript/ToolBar.js' . '"></script>';

        if ($_SESSION['toolbar_state'] == 'hide')
        {
            $html[] = '<script type="text/javascript">var hide = "true";</script>';
        }
        else
        {
            $html[] = '<script type="text/javascript">var hide = "false";</script>';
        }

        $html[] = '<div class="clear">&nbsp;</div>';

        return implode(PHP_EOL, $html);
    }

    public function display_tool($tool)
    {
        $html = array();

        $parent = $this->get_parent();
        // $course = $parent->get_course();

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

        $html[] = '<li class="tool_list_menu" style="padding: 0px 0px 2px 0px;">';

        $html[] = '<a href="' . $parent->get_url(
            array(
                Application :: PARAM_ACTION => Manager :: ACTION_VIEW_COURSE,
                Manager :: PARAM_TOOL => $tool->get_name()),
            array(
                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION,
                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID,
                Manager :: PARAM_CATEGORY,
                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_BROWSER_TYPE),
            true) . '" title="' . $title . '">';

        if ($this->display_menu_icons())
        {
            $html[] = '<img src="' . Theme :: getInstance()->getImagePath(
                \Chamilo\Application\Weblcms\Tool\Manager :: get_tool_type_namespace($tool->get_name()),
                'Logo/' . $tool_image) . '" style="vertical-align: middle;" alt="' . $title . '"/> ';
        }

        if ($this->display_menu_text())
        {
            $html[] = $title;
        }

        $html[] = '</a>';
        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    public function load_menu_properties()
    {
        $course_settings_controller = CourseSettingsController :: get_instance();
        $menu_style = $course_settings_controller->get_course_setting(
            $this->get_parent()->get_course()->get_id(),
            CourseSettingsConnector :: MENU_LAYOUT);

        $properties = array();

        switch ($menu_style)
        {
            case CourseSettingsConnector :: MENU_LAYOUT_LEFT_WITH_ICONS :
                $properties['style'] = 'left';
                $properties['icons'] = true;
                $properties['text'] = false;
                break;
            case CourseSettingsConnector :: MENU_LAYOUT_LEFT_BOTH :
                $properties['style'] = 'left';
                $properties['icons'] = true;
                $properties['text'] = true;
                break;
            case CourseSettingsConnector :: MENU_LAYOUT_LEFT_WITH_TEXT :
                $properties['style'] = 'left';
                $properties['icons'] = false;
                $properties['text'] = true;
                break;

            case CourseSettingsConnector :: MENU_LAYOUT_RIGHT_WITH_ICONS :
                $properties['style'] = 'right';
                $properties['icons'] = true;
                $properties['text'] = false;
                break;
            case CourseSettingsConnector :: MENU_LAYOUT_RIGHT_BOTH :
                $properties['style'] = 'right';
                $properties['icons'] = true;
                $properties['text'] = true;
                break;
            case CourseSettingsConnector :: MENU_LAYOUT_RIGHT_WITH_TEXT :
                $properties['style'] = 'right';
                $properties['icons'] = false;
                $properties['text'] = true;
                break;

            default :
                $properties['style'] = 'left';
                $properties['icons'] = true;
                $properties['text'] = true;
                break;
        }

        return $properties;
    }

    public function get_menu_properties()
    {
        return $this->menu_properties;
    }

    public function get_menu_style()
    {
        $properties = $this->get_menu_properties();
        return $properties['style'];
    }

    public function display_menu_icons()
    {
        $properties = $this->get_menu_properties();
        return $properties['icons'];
    }

    public function display_menu_text()
    {
        $properties = $this->get_menu_properties();
        return $properties['text'];
    }
}
