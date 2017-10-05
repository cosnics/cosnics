<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseSections\Component;

use Chamilo\Application\Weblcms\Storage\DataClass\CourseSection;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTool;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseSections\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseSections\Storage\DataManager;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use HTML_Table;

/**
 *
 * @package application.lib.weblcms.tool.course_sections.component
 */
class ChangeSectionComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->get_course()->is_course_admin($this->get_parent()->get_user()))
        {
            throw new \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException();
        }

        $target = $_POST['target'];
        $source = $_POST['source'];

        $targets = explode('_', $target);
        $target = $targets[1];

        $sources = explode('_', $source);
        $source = $sources[1];

        $tools = DataManager::retrieves(CourseTool::class_name(), new DataClassRetrievesParameters());

        foreach ($tools as $tool)
        {
            if ($this->group_inactive)
            {
                if ($this->course->get_layout() > 2)
                {
                    if ($tool->visible)
                    {
                        $tools[$tool->section][] = $tool;
                    }
                    else
                    {
                        $tools[CourseSection::TYPE_DISABLED][] = $tool;
                    }
                }
                else
                    $tools[$tool->section][] = $tool;
            }
            else
            {
                $tools[$tool->section][] = $tool;
            }
        }

        $section = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            CourseSection::class_name(),
            (int) $target);

        $this->show_section_tools($section, $tools[$section->get_id()]);
    }

    private function show_section_tools($section, $tools)
    {
        $parent = $this->get_parent();

        $is_course_admin = $parent->get_course()->is_course_admin($parent->get_user());
        $course = $parent->get_course();
        $number_of_columns = ($course->get_layout() % 2 == 0) ? 3 : 2;

        $table = new HTML_Table('style="width: 100%;"');
        $table->setColCount($number_of_columns);
        $count = 0;
        foreach ($tools as $tool)
        {
            if ($tool->visible || $section->get_name() == 'course_admin')
            {
                $lcms_action = 'make_invisible';
                $visible_image = 'Action/Visible';
                $new = '';
                if ($parent->tool_has_new_publications($tool->name))
                {
                    $new = 'New';
                }
                $tool_image = 'Tool' . $tool->name . $new;
                $link_class = '';
            }
            else
            {
                $lcms_action = 'make_visible';
                $visible_image = 'Action/Invisible';
                $tool_image = 'Tool' . $tool->name . 'Na';
                $link_class = ' class="invisible"';
            }
            $title = htmlspecialchars(
                Translation::get(\Chamilo\Application\Weblcms\Tool\Manager::type_to_class($tool->name) . 'Title'));
            $row = $count / $number_of_columns;
            $col = $count % $number_of_columns;
            $html = array();
            if ($is_course_admin || $tool->visible)
            {
                if ($section->get_type() == CourseSection::TYPE_TOOL)
                {
                    $html[] = '<div id="tool_' . $tool->id . '" class="tool" style="display:inline">';

                    $id = 'id="drag_' . $tool->id . '"';
                }

                // Show visibility-icon
                if ($is_course_admin && $section->get_name() != 'course_admin')
                {
                    $html[] = '<a href="' . $parent->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Manager::PARAM_COMPONENT_ACTION => $lcms_action,
                            \Chamilo\Application\Weblcms\Manager::PARAM_TOOL => $tool->name)) . '"><img src="' .
                         Theme::getInstance()->getCommonImagePath($visible_image) .
                         '" style="vertical-align: middle;" alt=""/></a>';
                    $html[] = '&nbsp;&nbsp;&nbsp;';
                }

                // Show tool-icon + name

                $html[] = '<img ' . $id . ' src="' . Theme::getInstance()->getImagePath(
                    'Chamilo\Application\Weblcms\Tool\Implementation\CourseSections',
                    $tool_image) . '" style="vertical-align: middle;" alt="' . $title . '"/>';
                $html[] = '&nbsp;';
                $html[] = '<a href="' . $parent->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Manager::PARAM_COMPONENT_ACTION => null,
                        \Chamilo\Application\Weblcms\Manager::PARAM_TOOL => $tool->name),
                    true) . '" ' . $link_class . '>';
                $html[] = $title;
                $html[] = '</a>';
                if ($section->get_type() == CourseSection::TYPE_TOOL)
                {
                    $html[] = '</div>';
                    $html[] = '<script type="text/javascript">$("#tool_' . $tool->id .
                         '").draggable({ handle: "div", revert: true, helper: "original"});</script>';
                }

                $table->setCellContents($row, $col, implode(PHP_EOL, $html));
                $table->updateColAttributes($col, 'style="width: ' . floor(100 / $number_of_columns) . '%;"');
                $count ++;
            }
        }
        $table->display();
    }
}
