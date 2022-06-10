<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentRatingQuestion\Form;

use Chamilo\Core\Repository\ContentObject\AssessmentRatingQuestion\Storage\DataClass\AssessmentRatingQuestion;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\Form\FormTab;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package repository.lib.content_object.rating_question
 */

/**
 * This class represents a form to create or update open questions
 */
class AssessmentRatingQuestionForm extends ContentObjectForm
{
    const PROPERTY_RATING_TYPE = 'ratingtype';
    const RATING_TYPE_PERCENTAGE = 0;
    const RATING_TYPE_RATING = 1;

    public function addHintTab()
    {
        $this->getTabsCollection()->add(
            new FormTab(
                'add-hint', Translation::get('AddHint'), new FontAwesomeGlyph('magic', array('fa-sm')), 'buildHintForm'
            )
        );
    }

    public function buildBasicQuestionForm()
    {
        $this->addElement('category', Translation::get('Properties'));

        $elem[] = $this->createElement(
            'radio', 'ratingtype', null, Translation::get('Percentage') . ' (0-100)', 0,
            array('onclick' => 'javascript:hide_controls(\'buttons\')')
        );
        $elem[] = $this->createElement(
            'radio', 'ratingtype', null, Translation::get('Rating'), 1,
            array('onclick' => 'javascript:show_controls(\'buttons\')')
        );
        $this->addGroup($elem, 'type', Translation::get('Type', null, StringUtilities::LIBRARIES), '', false);

        $this->addElement('html', '<div id="buttons" class="form-group clearfix">');

        $this->addElement('html', '<div class="col-xs-12 col-sm-4 col-md-3 col-lg-2 form-label control-label"></div>');
        $this->addElement(
            'html', '<div class="col-xs-12 col-sm-8 col-md-9 col-lg-10 formw">'
        );

        $this->addElement('html', '<div class="row">');
        $this->addElement('html', '<div class="col-sm-12 col-md-6">');

        $glyph = new FontAwesomeGlyph('long-arrow-alt-down', [], null, 'fas');

        $this->addElement('html', '<div class="input-group">');
        $this->addElement('html', '<div class="input-group-addon">' . $glyph->render() . '</div>');

        $this->addElement(
            'text', AssessmentRatingQuestion::PROPERTY_LOW, null,
            array('class' => 'rating_question_low_value form-control')
        );

        $this->addElement('html', '</div>');
        $this->addElement('html', '</div>');

        $this->addElement('html', '<div class="col-sm-12 col-md-6">');

        $glyph = new FontAwesomeGlyph('long-arrow-alt-up', [], null, 'fas');

        $this->addElement('html', '<div class="input-group">');
        $this->addElement('html', '<div class="input-group-addon">' . $glyph->render() . '</div>');

        $this->addElement(
            'text', AssessmentRatingQuestion::PROPERTY_HIGH, null,
            array('class' => 'rating_question_high_value form-control')
        );

        $this->addElement('html', '</div>');

        $this->addElement('html', '</div>');

        $this->addElement('html', '</div>');
        $this->addElement('html', '</div>');

        $this->get_renderer()->setElementTemplate('{element}', AssessmentRatingQuestion::PROPERTY_LOW);
        $this->get_renderer()->setElementTemplate('{element}', AssessmentRatingQuestion::PROPERTY_HIGH);

        $this->addElement('html', '</div>');

        $this->add_textfield(AssessmentRatingQuestion::PROPERTY_CORRECT, Translation::get('CorrectValue'), false);

        $html_editor_options = [];
        $html_editor_options['width'] = '595';
        $html_editor_options['height'] = '100';
        $html_editor_options['collapse_toolbar'] = true;
        $html_editor_options['show_tags'] = false;

        $this->add_html_editor(
            AssessmentRatingQuestion::PROPERTY_FEEDBACK, Translation::get('Feedback'), false, $html_editor_options
        );

        $this->addElement(
            'html', "<script type=\"text/javascript\">
			/* <![CDATA[ */
			hide_controls('buttons');
			function show_controls(elem) {
				el = document.getElementById(elem);
				el.style.display='';
			}
			function hide_controls(elem) {
				el = document.getElementById(elem);
				el.style.display='none';
			}
			/* ]]> */
				</script>\n"
        );

        $this->addRule(
            AssessmentRatingQuestion::PROPERTY_LOW,
            Translation::get('ThisFieldShouldBeNumeric', null, StringUtilities::LIBRARIES), 'numeric'
        );

        $this->addRule(
            AssessmentRatingQuestion::PROPERTY_HIGH,
            Translation::get('ThisFieldShouldBeNumeric', null, StringUtilities::LIBRARIES), 'numeric'
        );

        $this->addRule(
            AssessmentRatingQuestion::PROPERTY_CORRECT,
            Translation::get('ThisFieldShouldBeNumeric', null, StringUtilities::LIBRARIES), 'numeric'
        );

        $this->addElement(
            'html', ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath(
                'Chamilo\Core\Repository\ContentObject\AssessmentRatingQuestion', true
            ) . 'AssessmentRatingQuestion.js'
        )
        );
    }

    public function buildHintForm()
    {
        $htmlEditorOptions = [];
        $htmlEditorOptions['width'] = '100%';
        $htmlEditorOptions['height'] = '100';
        $htmlEditorOptions['collapse_toolbar'] = true;
        $htmlEditorOptions['show_tags'] = false;

        $this->add_html_editor(
            AssessmentRatingQuestion::PROPERTY_HINT,
            Translation::get('Hint', [], ClassnameUtilities::getInstance()->getNamespaceFromObject($this)), false,
            $htmlEditorOptions
        );
    }

    protected function build_creation_form($htmleditor_options = [], $in_tab = false)
    {
        parent::build_creation_form($this->getDescriptionHtmlEditorOptions());
        $this->buildBasicQuestionForm();
    }

    protected function build_editing_form($htmleditor_options = [], $in_tab = false)
    {
        parent::build_editing_form($this->getDescriptionHtmlEditorOptions());
        $this->buildBasicQuestionForm();
    }

    public function create_content_object()
    {
        $this->set_content_object_properties(new AssessmentRatingQuestion());

        return parent::create_content_object();
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

    public function generateTabs()
    {
        $this->addDefaultTab();
        $this->addHintTab();
        $this->addInstructionsTab();
        $this->addMetadataTabs();
    }

    public function setDefaults($defaults = [], $filter = null)
    {
        $object = $this->get_content_object();

        $defaults[AssessmentRatingQuestion::PROPERTY_FEEDBACK] = $object->get_feedback();
        $defaults[AssessmentRatingQuestion::PROPERTY_HINT] = $object->get_hint();
        $defaults[AssessmentRatingQuestion::PROPERTY_LOW] = $object->get_low();
        $defaults[AssessmentRatingQuestion::PROPERTY_HIGH] = $object->get_high();
        $defaults[AssessmentRatingQuestion::PROPERTY_CORRECT] = $object->get_correct();

        if (($object->get_low() == 0 || is_null($object->get_low())) &&
            ($object->get_high() == 100 || is_null($object->get_high())))
        {
            $defaults['ratingtype'] = 0;
        }
        else
        {
            $defaults['ratingtype'] = 1;
        }

        parent::setDefaults($defaults);
    }

    protected function set_content_object_properties($object)
    {
        $values = $this->exportValues();

        $object->set_feedback($values[AssessmentRatingQuestion::PROPERTY_FEEDBACK]);
        $object->set_hint($values[AssessmentRatingQuestion::PROPERTY_HINT]);

        if ($values[self::PROPERTY_RATING_TYPE] == self::RATING_TYPE_PERCENTAGE)
        {
            $object->set_low(0);
            $object->set_high(100);
        }
        else
        {
            if (isset($values[AssessmentRatingQuestion::PROPERTY_LOW]) &&
                $values[AssessmentRatingQuestion::PROPERTY_LOW] != '')
            {
                $object->set_low($values[AssessmentRatingQuestion::PROPERTY_LOW]);
            }
            else
            {
                $object->set_low(0);
            }

            if (isset($values[AssessmentRatingQuestion::PROPERTY_HIGH]) &&
                $values[AssessmentRatingQuestion::PROPERTY_HIGH] != '')
            {
                $object->set_high($values[AssessmentRatingQuestion::PROPERTY_HIGH]);
            }
            else
            {
                $object->set_high(100);
            }
        }

        if (isset($values[AssessmentRatingQuestion::PROPERTY_CORRECT]))
        {
            $object->set_correct($values[AssessmentRatingQuestion::PROPERTY_CORRECT]);
        }
        else
        {
            $object->set_correct(null);
        }

        $this->set_content_object($object);
    }

    public function update_content_object()
    {
        $this->set_content_object_properties($this->get_content_object());

        return parent::update_content_object();
    }
}
