<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentOpenQuestion\Form;

use Chamilo\Core\Repository\ContentObject\AssessmentOpenQuestion\Storage\DataClass\AssessmentOpenQuestion;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Format\Tabs\DynamicFormTab;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;

/**
 *
 * @package repository.lib.content_object.assessment_open_question
 */
/**
 * This class represents a form to create or update open questions
 */
class AssessmentOpenQuestionForm extends ContentObjectForm
{

    public function setDefaults($defaults = array (), $filter = null)
    {
        $object = $this->get_content_object();

        if ($object->get_id() != null)
        {
            $defaults[AssessmentOpenQuestion::PROPERTY_HINT] = $object->get_hint();
            $defaults[AssessmentOpenQuestion::PROPERTY_QUESTION_TYPE] = $object->get_question_type();
            $defaults[AssessmentOpenQuestion::PROPERTY_FEEDBACK] = $object->get_feedback();
        }
        else
        {
            $defaults[AssessmentOpenQuestion::PROPERTY_QUESTION_TYPE] = AssessmentOpenQuestion::TYPE_OPEN;
        }

        parent::setDefaults($defaults);
    }

    private function buildBasicForm()
    {
        $this->addElement('category', Translation::get('Properties'));
        $types = AssessmentOpenQuestion::get_types();
        $choices = [];
        foreach ($types as $type_id => $type_label)
        {
            $choices[] = $this->createElement(
                'radio',
                AssessmentOpenQuestion::PROPERTY_QUESTION_TYPE,
                '',
                $type_label,
                $type_id);
        }
        $this->addGroup($choices, null, Translation::get('OpenQuestionQuestionType'), '', false);

        $html_editor_options = [];
        $html_editor_options['width'] = '595';
        $html_editor_options['height'] = '100';
        $html_editor_options['collapse_toolbar'] = true;
        $html_editor_options['show_tags'] = false;

        $this->add_html_editor(
            AssessmentOpenQuestion::PROPERTY_FEEDBACK,
            Translation::get('Feedback'),
            false,
            $html_editor_options);
    }

    public function build_creation_form($htmleditor_options = [], $in_tab = false)
    {
        parent::build_creation_form($this->getDescriptionHtmlEditorOptions());
        $this->buildBasicForm();
    }

    // Inherited
    public function build_editing_form($htmleditor_options = [], $in_tab = false)
    {
        parent::build_editing_form($this->getDescriptionHtmlEditorOptions());
        $this->buildBasicForm();
    }

    protected function getDescriptionHtmlEditorOptions()
    {
        $htmlEditorOptions = [];
        $htmlEditorOptions['width'] = '100%';
        $htmlEditorOptions['height'] = '100';
        $htmlEditorOptions['collapse_toolbar'] = false;
        $htmlEditorOptions['show_tags'] = false;

        return $htmlEditorOptions;
    }

    protected function processSubmittedValues($contentObject)
    {
        $values = $this->exportValues();
        $contentObject->set_hint($values[AssessmentOpenQuestion::PROPERTY_HINT]);
        $contentObject->set_question_type($values[AssessmentOpenQuestion::PROPERTY_QUESTION_TYPE]);
        $contentObject->set_feedback($values[AssessmentOpenQuestion::PROPERTY_FEEDBACK]);

        $this->set_content_object($contentObject);

        return $contentObject;
    }

    // Inherited
    public function create_content_object()
    {
        $this->processSubmittedValues(new AssessmentOpenQuestion());
        return parent::create_content_object();
    }

    public function update_content_object()
    {
        $this->processSubmittedValues($this->get_content_object());
        return parent::update_content_object();
    }

    public function prepareTabs()
    {
        $this->addDefaultTab();
        $this->addHintTab();
        $this->addInstructionsTab();
        $this->addMetadataTabs();
    }

    public function addHintTab()
    {
        $this->getTabsGenerator()->add_tab(
            new DynamicFormTab(
                'add-hint',
                Translation::get('AddHint'),
                new FontAwesomeGlyph('magic', array('fa-sm')),
                'buildHintForm'));
    }

    public function buildHintForm()
    {
        $htmlEditorOptions = [];
        $htmlEditorOptions['width'] = '100%';
        $htmlEditorOptions['height'] = '100';
        $htmlEditorOptions['collapse_toolbar'] = true;
        $htmlEditorOptions['show_tags'] = false;

        $this->add_html_editor(
            AssessmentOpenQuestion::PROPERTY_HINT,
            Translation::get('Hint', [], ClassnameUtilities::getInstance()->getNamespaceFromObject($this)),
            false,
            $htmlEditorOptions);
    }
}
