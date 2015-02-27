<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Display\Component\Viewer;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass\ComplexPage;
use Chamilo\Core\Repository\ContentObject\Survey\Display\PageDisplay;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class Form extends FormValidator
{
    const FORM_NAME = 'survey_viewer_form';

    private $parent;

    function __construct($parent, $action)
    {
        parent :: __construct(self :: FORM_NAME, 'post', $action, '', array('autocomplete' => 'off'));
        $this->parent = $parent;
        $this->add_buttons();
        
        if ($this->parent->get_current_complex_content_object_item() instanceof ComplexPage)
        {
            $this->buildPageForm();
        }
        else
        {
            $this->buildSurveyForm();
        }
        
        $this->add_buttons();
    }

    function buildPageForm()
    {
        $page_display = PageDisplay :: factory($this, $this->parent->get_current_complex_content_object_path_node());
        $page_display->run();
    }

    function buildSurveyForm()
    {
        $current_content_object = $this->parent->get_current_content_object();
        
        $html = array();
        $html[] = '<div class="clear"></div>';
        $html[] = '<div class="content_object" style="background-image: url(' . $current_content_object->get_icon_path() .
             ');">';
        $html[] = '<div class="title">' . $current_content_object->get_title() . '</div>';
        $html[] = '<div class="description" style="overflow: auto;">';
        $html[] = '<div class="description">';
        $html[] = $current_content_object->get_description();
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $this->addElement('html', implode(PHP_EOL, $html));
    }

    public function add_buttons()
    {
        $buttons = array();
        
        if ($this->parent->get_current_step() != 1)
        {
            $buttons[] = $this->createElement(
                'style_submit_button', 
                'back', 
                Translation :: get('PreviousPage', null, Utilities :: COMMON_LIBRARIES), 
                array('class' => 'previous'));
        }
        
        if ($this->parent->get_current_step() != $this->parent->count_steps())
        {
            $buttons[] = $this->createElement(
                'style_submit_button', 
                'next', 
                Translation :: get('NextPage', null, Utilities :: COMMON_LIBRARIES), 
                array('class' => 'normal next'));
        }
        else
        {
            $buttons[] = $this->createElement(
                'style_submit_button', 
                'submit', 
                Translation :: get('Finish', null, Utilities :: COMMON_LIBRARIES), 
                array('class' => 'positive submit'));
        }
        
        if (count($buttons) > 0)
        {
            $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        }
        
        $renderer = $this->defaultRenderer();
        $renderer->setElementTemplate('<div style="float: right;">{element}</div><br /><br />', 'buttons');
        $renderer->setGroupElementTemplate('{element}', 'buttons');
    }

    public function get_parent()
    {
        return $this->parent;
    }
}
?>