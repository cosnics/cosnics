<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentOpenQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display;

use Chamilo\Core\Repository\Component\RepositoryViewerComponent;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\QuestionDisplay;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\ContentObject\AssessmentOpenQuestion\Storage\DataClass\AssessmentOpenQuestion;
use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Core\Repository\ContentObject\AssessmentOpenQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Display extends QuestionDisplay
{
    public function add_document($clo_question, $formvalidator)
    {
        $type = $this->get_question()->get_question_type();

        if ($type == AssessmentOpenQuestion::TYPE_OPEN_WITH_DOCUMENT)
        {
            $html[] = '<br /><p>';
            $html[] = '<p>';
            $html[] = '<strong>';
            $html[] = Translation::get('SelectDocument');
            $html[] = '</strong>';
            $html[] = '</p>';

            $formvalidator->addElement('html', implode(PHP_EOL, $html));
        }

        $name_1 = $clo_question->get_id() . '_1';
        $name_2 = $clo_question->get_id() . '_2';

        $group = [];
        $group[] = $formvalidator->createElement(
            'text', ($name_2 . '_title'), '',
            ['class' => 'select_file_text', 'disabled' => 'disabled', 'style' => 'width: 200px; height: 20px']
        );
        $group[] = $formvalidator->createElement('hidden', $name_2);

        $link = new Redirect(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_REPOSITORY_VIEWER,
                RepositoryViewerComponent::PARAM_ELEMENT_NAME => $name_2
            ]
        );

        $uploadGlyph = new FontAwesomeGlyph('upload', [], null, 'fas');

        $group[] = $formvalidator->createElement(
            'static', null, null,
            '<a class="btn btn-default" onclick="openPopup(\'' . addslashes($link->getUrl()) . '\');">' .
            $uploadGlyph->render() . ' ' . Translation::get('BrowseContentObjects') . '</a>'
        );

        $formvalidator->addGroup($group, '');
    }

    public function add_footer()
    {
        $formvalidator = $this->get_formvalidator();

        if ($this->get_question()->has_hint() && $this->get_configuration()->allow_hints())
        {
            $hint_name = 'hint_' . $this->get_complex_content_object_question()->get_id();
            $glyph = new FontAwesomeGlyph('gift', [], null, 'fas');

            $html[] = '<div class="panel-body panel-body-assessment-hint">';
            $html[] = '<a id="' . $hint_name . '" class="btn btn-default">' . $glyph->render() . ' ' .
                Translation::get('GetAHint') . '</a>';
            $html[] = '</div>';

            $footer = implode(PHP_EOL, $html);
            $formvalidator->addElement('html', $footer);
        }

        parent::add_footer();
    }

    public function add_html_editor($clo_question, $formvalidator)
    {
        $html_editor_options = [];
        $html_editor_options['width'] = '100%';
        $html_editor_options['height'] = 150;
        $html_editor_options['collapse_toolbar'] = true;

        $element_template = [];
        $element_template[] =
            '<div><!-- BEGIN error --><small class="text-danger">{error}</small><br /><!-- END error -->	{element}';
        $element_template[] = '<div class="clearfix"></div>';
        $element_template[] = '<div class="form_feedback"></div>';
        $element_template[] = '<div class="clearfix"></div>';
        $element_template[] = '</div>';
        $element_template = implode(PHP_EOL, $element_template);
        $renderer = $this->get_renderer();

        $name = $clo_question->get_id() . '_0';
        $formvalidator->add_html_editor($name, '', false, $html_editor_options);
        $renderer->setElementTemplate($element_template, $name);
    }

    public function add_question_form()
    {
        $clo_question = $this->get_complex_content_object_question();
        $question = $this->get_question();
        $type = $question->get_question_type();
        $formvalidator = $this->get_formvalidator();

        $formvalidator->addElement('html', '<div class="panel-body">');
        $formvalidator->addElement('html', $this->get_instruction());

        switch ($type)
        {
            case AssessmentOpenQuestion::TYPE_DOCUMENT :
                $this->add_document($clo_question, $formvalidator);
                break;
            case AssessmentOpenQuestion::TYPE_OPEN :
                $this->add_html_editor($clo_question, $formvalidator);
                break;
            case AssessmentOpenQuestion::TYPE_OPEN_WITH_DOCUMENT :
                $this->add_html_editor($clo_question, $formvalidator);
                $this->add_document($clo_question, $formvalidator);
                break;
        }

        $formvalidator->addElement('html', '</div>');

        $formvalidator->addElement(
            'html', ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath(Assessment::CONTEXT, true) . 'GiveHint.js'
        )
        );
    }

    public function get_instruction()
    {
        $instruction = [];
        $question = $this->get_question();
        $type = $question->get_question_type();

        if ($question->has_description())
        {
            $instruction[] = '<p>';
            $instruction[] = '<strong>';

            if ($type == AssessmentOpenQuestion::TYPE_DOCUMENT)
            {
                $instruction[] = Translation::get('SelectDocument');
            }
            else
            {
                $instruction[] = Translation::get('EnterAnswer');
            }

            $instruction[] = '</strong>';
            $instruction[] = '</p>';
        }
        else
        {
            $instruction = [];
        }

        return implode(PHP_EOL, $instruction);
    }

    public function needsDescriptionBorder()
    {
        return true;
    }
}
