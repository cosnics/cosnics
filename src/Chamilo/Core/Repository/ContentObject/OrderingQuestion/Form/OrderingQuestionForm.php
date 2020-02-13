<?php
namespace Chamilo\Core\Repository\ContentObject\OrderingQuestion\Form;

use Chamilo\Core\Repository\ContentObject\OrderingQuestion\Storage\DataClass\OrderingQuestion;
use Chamilo\Core\Repository\ContentObject\OrderingQuestion\Storage\DataClass\OrderingQuestionOption;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\DynamicFormTab;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package repository.lib.content_object.ordering_question
 */
class OrderingQuestionForm extends ContentObjectForm
{

    protected function build_creation_form()
    {
        parent::build_creation_form();
        $this->addElement('category', Translation::get('Items'));
        $this->addElement(
            'html', ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\OrderingQuestion', true) .
            'OrderingQuestion.js'
        )
        );
        $this->add_options();
        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        parent::build_editing_form();
        $this->addElement('category', Translation::get('Items'));
        $this->addElement(
            'html', ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\OrderingQuestion', true) .
            'OrderingQuestion.js'
        )
        );
        $this->add_options();
        $this->addElement('category');
    }

    public function setDefaults($defaults = array())
    {
        if (!$this->isSubmitted())
        {
            $object = $this->get_content_object();
            $defaults[OrderingQuestion::PROPERTY_HINT] = $object->get_hint();

            if ($object->get_number_of_options() != 0)
            {
                $options = $object->get_options();
                foreach ($options as $index => $option)
                {
                    $defaults[OrderingQuestionOption::PROPERTY_VALUE][$index] = $option->get_value();
                    $defaults[OrderingQuestionOption::PROPERTY_SCORE][$index] = $option->get_score();
                    $defaults[OrderingQuestionOption::PROPERTY_FEEDBACK][$index] = $option->get_feedback();
                    $defaults[OrderingQuestionOption::PROPERTY_ORDER][$index] = $option->get_order();
                }
            }
            else
            {
                $number_of_options = intval($_SESSION['ordering_number_of_options']);

                for ($option_number = 0; $option_number < $number_of_options; $option_number ++)
                {
                    $defaults[OrderingQuestionOption::PROPERTY_SCORE][$option_number] = 1;
                    $defaults[OrderingQuestionOption::PROPERTY_ORDER][$option_number] = $option_number + 1;
                }
            }
        }
        parent::setDefaults($defaults);
    }

    public function create_content_object()
    {
        $object = new OrderingQuestion();
        $object->set_hint($this->exportValue(OrderingQuestion::PROPERTY_HINT));
        $this->set_content_object($object);
        $this->add_options_to_object();

        return parent::create_content_object();
    }

    public function update_content_object()
    {
        $this->get_content_object()->set_hint($this->exportValue(OrderingQuestion::PROPERTY_HINT));
        $this->add_options_to_object();

        return parent::update_content_object();
    }

    private function add_options_to_object()
    {
        $object = $this->get_content_object();
        $values = $this->exportValues();
        $options = array();
        foreach ($values[OrderingQuestionOption::PROPERTY_VALUE] as $option_id => $value)
        {
            $order = $values[OrderingQuestionOption::PROPERTY_ORDER][$option_id];
            $score = $values[OrderingQuestionOption::PROPERTY_SCORE][$option_id];
            $feedback = $values[OrderingQuestionOption::PROPERTY_FEEDBACK][$option_id];

            $options[] = new OrderingQuestionOption($value, $order, $score, $feedback);
        }
        $object->set_options($options);
    }

    public function validate()
    {
        if (isset($_POST['add']) || isset($_POST['remove']) || isset($_POST['change_answer_type']))
        {
            return false;
        }

        return parent::validate();
    }

    /**
     * Adds the form-fields to the form to provide the possible options for this ordering question
     */
    private function add_options()
    {
        $renderer = $this->defaultRenderer();

        if (!$this->isSubmitted())
        {
            unset($_SESSION['ordering_number_of_options']);
            unset($_SESSION['ordering_skip_options']);
        }
        if (!isset($_SESSION['ordering_number_of_options']))
        {
            $_SESSION['ordering_number_of_options'] = 3;
        }
        if (!isset($_SESSION['ordering_skip_options']))
        {
            $_SESSION['ordering_skip_options'] = array();
        }
        if (isset($_POST['add']))
        {
            $_SESSION['ordering_number_of_options'] = $_SESSION['ordering_number_of_options'] + 1;
        }
        if (isset($_POST['remove']))
        {
            $indexes = array_keys($_POST['remove']);
            $_SESSION['ordering_skip_options'][] = $indexes[0];
        }
        $object = $this->get_content_object();
        if (!$this->isSubmitted() && $object->get_number_of_options() != 0)
        {
            $_SESSION['ordering_number_of_options'] = $object->get_number_of_options();
        }
        $number_of_options = intval($_SESSION['ordering_number_of_options']);

        $this->addElement(
            'hidden', 'ordering_number_of_options', $_SESSION['ordering_number_of_options'],
            array('id' => 'ordering_number_of_options')
        );

        $buttons = array();
        // Notice: The [] are added to this element name so we don't have to deal with the _x and _y suffixes added when
        // clicking an image button
        $buttons[] = $this->createElement(
            'style_button', 'add[]', Translation::get('AddItem'), array('id' => 'add_option'), null,
            new FontAwesomeGlyph('plus')
        );
        $this->addGroup($buttons, 'question_buttons', null, '', false);

        $html_editor_options = array();
        $html_editor_options['width'] = '100%';
        $html_editor_options['height'] = '65';
        $html_editor_options['collapse_toolbar'] = true;

        $table_header = array();
        $table_header[] = '<table class="table table-striped table-bordered table-hover table-data">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th>' . Translation::get('Item') . '</th>';
        $table_header[] = '<th class="numeric">' . Translation::get('Order') . '</th>';
        $table_header[] = '<th>' . Translation::get('Feedback') . '</th>';
        $table_header[] = '<th class="numeric">' . Translation::get('Score') . '</th>';
        $table_header[] = '<th class="action"></th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->addElement('html', implode(PHP_EOL, $table_header));

        $select_options = array();
        for ($i = 1; $i <= $number_of_options; $i ++)
        {
            $select_options[$i] = $i;
        }

        for ($option_number = 0; $option_number < $number_of_options; $option_number ++)
        {
            if (!in_array($option_number, $_SESSION['ordering_skip_options']))
            {
                $group = array();

                $group[] = $this->create_html_editor(
                    OrderingQuestionOption::PROPERTY_VALUE . '[' . $option_number . ']', Translation::get('Item'),
                    $html_editor_options
                );
                $group[] = &$this->createElement(
                    'select', OrderingQuestionOption::PROPERTY_ORDER . '[' . $option_number . ']',
                    Translation::get('Rank'), $select_options
                );
                $group[] = $this->create_html_editor(
                    OrderingQuestionOption::PROPERTY_FEEDBACK . '[' . $option_number . ']',
                    Translation::get('Feedback'), $html_editor_options
                );
                $group[] = &$this->createElement(
                    'text', OrderingQuestionOption::PROPERTY_SCORE . '[' . $option_number . ']',
                    Translation::get('Score'), 'size="2"  class="input_numeric"'
                );

                if ($number_of_options - count($_SESSION['ordering_skip_options']) > 2)
                {
                    $group[] = &$this->createElement(
                        'image', 'remove[' . $option_number . ']',
                        Theme::getInstance()->getCommonImagePath('Action/Delete'),
                        array('class' => 'remove_option', 'id' => 'remove_' . $option_number)
                    );
                }
                else
                {
                    $group[] = &$this->createElement(
                        'static', null, null,
                        '<img src="' . Theme::getInstance()->getCommonImagePath('Action/DeleteNa') .
                        '" class="remove_option" />'
                    );
                }

                $this->addGroup($group, OrderingQuestionOption::PROPERTY_VALUE . '_' . $option_number, null, '', false);

                $error = '<tr><td colspan="5" class="error">' . Translation::get('ScoreBigger') . '</td></tr>';

                $this->addGroupRule(
                    OrderingQuestionOption::PROPERTY_VALUE . '_' . $option_number, array(
                        OrderingQuestionOption::PROPERTY_SCORE . '[' . $option_number . ']' => array(
                            array($error, 'number_compare', '>', 0)
                        )
                    )
                );

                $renderer->setElementTemplate(
                    '<!-- BEGIN error -->{error}<!-- END error --><tr id="option_' . $option_number . '" class="' .
                    ($option_number % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>',
                    OrderingQuestionOption::PROPERTY_VALUE . '_' . $option_number
                );
                $renderer->setGroupElementTemplate(
                    '<td>{element}</td>', OrderingQuestionOption::PROPERTY_VALUE . '_' . $option_number
                );
            }
        }

        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $this->addElement('html', implode(PHP_EOL, $table_footer));

        $this->addGroup($buttons, 'question_buttons', null, '', false);

        $renderer->setElementTemplate(
            '<div style="margin: 10px 0px 10px 0px;">{element}<div class="clear"></div></div>', 'question_buttons'
        );
        $renderer->setGroupElementTemplate(
            '<div style="float:left; text-align: center; margin-right: 10px;">{element}</div>', 'question_buttons'
        );
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
                'add-hint', Translation::get('AddHint'), new FontAwesomeGlyph('magic', array('ident-sm')),
                'buildHintForm'
            )
        );
    }

    public function buildHintForm()
    {
        $htmlEditorOptions = array();
        $htmlEditorOptions['width'] = '100%';
        $htmlEditorOptions['height'] = '100';
        $htmlEditorOptions['collapse_toolbar'] = true;
        $htmlEditorOptions['show_tags'] = false;

        $this->add_html_editor(
            OrderingQuestion::PROPERTY_HINT,
            Translation::get('Hint', array(), ClassnameUtilities::getInstance()->getNamespaceFromObject($this)), false,
            $htmlEditorOptions
        );
    }
}
