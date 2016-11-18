<?php
namespace Chamilo\Application\Weblcms\Renderer\ToolList\Type;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Renderer\ToolList\ToolListRenderer;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSection;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\DropdownButtonRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

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
     *
     * @return string
     */
    private function show_tools($tools)
    {
        $translator = Translation::getInstance();
        $themeUtilities = Theme::getInstance();
        
        $parent = $this->get_parent();
        $course = $parent->get_course();
        
        $currentTool = $parent->get_tool_id();
        $toolNamespace = \Chamilo\Application\Weblcms\Tool\Manager::get_tool_type_namespace($currentTool);
        
        $toolsButton = new DropdownButton(
            Translation::get('NavigateTo', null, Utilities::COMMON_LIBRARIES), 
            $themeUtilities->getImagePath($toolNamespace, 'Logo/' . Theme::ICON_MINI), 
            Button::DISPLAY_LABEL);
        
        $toolsButton->setDropdownClasses('dropdown-menu-right');
        
        usort(
            $tools, 
            function ($toolA, $toolB) use ($translator)
            {
                $translationA = $translator->getTranslation('TypeName', null, $toolA->getContext());
                $translationB = $translator->getTranslation('TypeName', null, $toolB->getContext());
                
                return strcmp($translationA, $translationB);
            });
        
        foreach ($tools as $tool)
        {
            if ($tool->get_section_type() == CourseSection::TYPE_ADMIN && ! $this->is_course_admin)
            {
                continue;
            }
            
            $new = '';
            if ($parent->tool_has_new_publications($tool->get_name()))
            {
                $new = 'New';
            }
            
            $tool_image = Theme::ICON_MINI . $new;
            
            $title = $translator->getTranslation('TypeName', null, $tool->getContext());
            
            $params = array(
                Application::PARAM_CONTEXT => Manager::context(), 
                Manager::PARAM_COURSE => $course->get_id(), 
                Application::PARAM_ACTION => Manager::ACTION_VIEW_COURSE, 
                Manager::PARAM_TOOL => $tool->get_name());
            
            $redirect = new Redirect($params, array(Manager::PARAM_CATEGORY), false);
            $url = $redirect->getUrl();
            
            $toolButton = new SubButton(
                $title, 
                new IdentGlyph(md5($tool->getContext())), 
                $url, 
                Button::DISPLAY_ICON_AND_LABEL);
            
            $toolsButton->addSubButton($toolButton);
        }
        
        $renderer = new DropdownButtonRenderer($toolsButton);
        
        return $renderer->render();
    }
}
