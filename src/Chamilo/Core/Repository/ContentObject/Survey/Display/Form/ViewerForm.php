<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Display\Form;

use Chamilo\Core\Repository\ContentObject\Survey\Display\PageDisplay;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class ViewerForm extends FormValidator
{
    const FORM_NAME = 'survey_viewer_form';

    private $parent;

    function __construct($parent, $action)
    {
        parent :: __construct(self :: FORM_NAME, 'post', $action, '', array('autocomplete' => 'off'));
        $this->parent = $parent;
        $this->addButtons();
        $this->buildForm();
        $this->addButtons();
    }

    function buildForm()
    {
        $surveyConfiguration = $this->parent->getApplicationConfiguration();
        $pageDisplay = PageDisplay :: factory(
            $this,
            $this->parent->get_current_complex_content_object_path_node(),
            $surveyConfiguration->getAnswerService());
        $pageDisplay->run();
    }

    public function addButtons()
    {
        $buttons = array();

        if ($this->parent->get_current_step() != 1)
        {
            $buttons[] = $this->createElement(
                'style_button',
                'back',
                Translation :: get('PreviousPage', null, Utilities :: COMMON_LIBRARIES),
                null,
                null,
                'chevron-left');
        }

        if ($this->parent->get_current_step() != $this->parent->count_steps())
        {
            $buttons[] = $this->createElement(
                'style_button',
                'next',
                Translation :: get('NextPage', null, Utilities :: COMMON_LIBRARIES),
                null,
                null,
                'chevron-right');
        }
        else
        {
            $buttons[] = $this->createElement(
                'style_submit_button',
                'submit',
                Translation :: get('Finish', null, Utilities :: COMMON_LIBRARIES),
                null,
                null,
                'arrow-right');
        }

        if (count($buttons) > 0)
        {
            $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        }

        $renderer = $this->defaultRenderer();
        $renderer->setElementTemplate('<div style="float: right;">{element}</div><br /><br />', 'buttons');
        $renderer->setGroupElementTemplate('{element}', 'buttons');
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function addHiddenField($name, $value)
    {
        $hidden = $this->createElement('hidden', 'param_' . $name, $value);
        $this->addElement($hidden);
    }
}
?>