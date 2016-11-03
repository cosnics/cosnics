<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Form;

use Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass\Page;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\QuestionDisplay;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass\Configuration;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataManager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @author Eduard.Vossen
 */
class ConfigureQuestionForm extends FormValidator
{
    const FORM_NAME = 'survey_builder_configure_question_form';
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'QuestionConfigurationUpdated';
    const RESULT_ERROR = 'QuestionConfigurationUpdateFailed';
    const TO_VISIBLE_QUESTION_ID = 'to_visible_question_id';

    private $parent;

    /**
     *
     * @var Configuration
     */
    private $configuration;

    /**
     *
     * @var Page
     */
    private $page;

    private $complex_question;

    private $complex_question_id;

    private $config_id;

    private $answer;

    private $complex_content_object_path_node;

    function __construct($parent, $page, $config_id)
    {
        parent :: __construct(self :: FORM_NAME, self :: FORM_METHOD_POST, $parent->get_url());

        $this->parent = $parent;

        $this->page = $page;

        $this->complex_content_object_path_node = $this->parent->get_current_node();

        $this->complex_question = $parent->get_current_complex_content_object_item();

        if ($config_id)
        {
            $this->form_type = self :: TYPE_EDIT;
        }
        else
        {
            $this->form_type = self :: TYPE_CREATE;
        }

        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->config_id = $config_id;
            $this->configuration = DataManager :: retrieve_by_id(Configuration :: class_name(), $config_id);
            $this->answer = $this->configuration->getAnswerMatches();
            $this->build_editing_form();
            $this->setDefaults();
        }
        elseif ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_creation_form();
        }
    }

    function build_basic_form()
    {
        $this->addElement('category', Translation :: get('Configuration'));
        $this->addElement('text', Configuration :: PROPERTY_NAME, Translation :: get('Name'), array("size" => "50"));
        $this->addRule(
            Configuration :: PROPERTY_NAME,
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
            'required');

        $this->add_html_editor(Configuration :: PROPERTY_DESCRIPTION, Translation :: get('Description'), false);

        $this->addElement(
            'html',
            '<div class="form-row"><div class="form-label"></div><div class="formw"><div class="element"  style="position : relative ; width : 100%" >');

        $question_display = QuestionDisplay :: factory(
            $this,
            $this->complex_content_object_path_node,
            $this->parent->getApplicationConfiguration()->getAnswerService());

        $question_display->run();

        $this->addElement('html', '</div></div><div class="clear">&nbsp;</div></div>');

        $nodes = $this->complex_content_object_path_node->get_siblings();

        foreach ($nodes as $node)
        {

            $complexQuestion = $node->get_complex_content_object_item();

            if (! $complexQuestion->is_visible())
            {
                $complexQuestion->set_visible(1);

                $complex_id = $complexQuestion->get_id();

                $checkbox = $this->createElement(
                    'checkbox',
                    self :: TO_VISIBLE_QUESTION_ID . '_' . $complex_id,
                    Translation :: get('MakeVisible'),
                    '',
                    array());
                $this->addElement($checkbox);

                $question_display = QuestionDisplay :: factory(
                    $this,
                    $node,
                    $this->parent->getApplicationConfiguration()->getAnswerService());

                $this->addElement(
                    'html',
                    '<div class="form-row"><div class="form-label"></div><div class="formw"><div class="element"  style="position : relative ; width : 100%" >');

                $question_display->run();

                $this->addElement('html', '</div></div><div class="clear">&nbsp;</div></div>');
            }
        }
    }

    function build_editing_form()
    {
        $this->build_basic_form();

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation :: get('Update'),
            null,
            null,
            'arrow-right');
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->addElement('category');
    }

    function build_creation_form()
    {
        $this->build_basic_form();

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES));
        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->addElement('category');
    }

    function update_config()
    {
        $values = $this->exportValues();

        $configs = $this->page->get_config();
        $config = $configs[$this->config_index];

        $config[Configuration :: PROPERTY_FROM_VISIBLE_QUESTION_ID] = $this->complex_question->get_id();
        $config[Configuration :: PROPERTY_TO_VISIBLE_QUESTION_IDS] = $this->get_to_visible_question_ids($values);
        $config[Configuration :: PROPERTY_ANSWER_MATCHES] = $this->create_answers($values);
        $duplicate = $this->is_duplicate($config);

        if (! $duplicate)
        {
            $config[Configuration :: PROPERTY_NAME] = $values[Configuration :: PROPERTY_NAME];
            $config[Configuration :: PROPERTY_DESCRIPTION] = $values[Configuration :: PROPERTY_DESCRIPTION];
            $config[Configuration :: PROPERTY_CONFIG_UPDATED] = time();
            $configs[$this->config_index] = $config;
            $this->page->set_config($configs);
            return $this->page->update();
        }
        else
        {
            $configs = $this->page->get_config();
            $config = $configs[$this->config_index];
            $config[Configuration :: PROPERTY_NAME] = $values[Configuration :: PROPERTY_NAME];
            $config[Configuration :: PROPERTY_DESCRIPTION] = $values[Configuration :: PROPERTY_DESCRIPTION];
            $config[Configuration :: PROPERTY_CONFIG_UPDATED] = time();
            $configs[$this->config_index] = $config;
            $this->page->set_config($configs);
            return $this->page->update();
        }
    }

    function create_configuration()
    {
        $values = $this->exportValues();

        $configuration = new Configuration();
        $configuration->setPageId($this->page->get_id());
        $configuration->setName($values[Configuration :: PROPERTY_NAME]);
        $configuration->setDescription($values[Configuration :: PROPERTY_DESCRIPTION]);
        $configuration->setComplexQuestionId($this->complex_question->get_id());
        $configuration->setToVisibleQuestionIds($this->get_to_visible_question_ids($values));
        $configuration->setAnswerMatches($this->create_answers($values));

        $duplicate = $this->isDuplicate($configuration);

        if (! $duplicate)
        {
            $time = time();
            $configuration->setUpdated($time);

            if ($this->configuration)
            {
                $configuration->set_id($this->configuration->get_id());
                $succes = $configuration->update();
            }
            else
            {
                $configuration->setCreated($time);
                $succes = $configuration->create();
            }

            if ($succes)
            {
                $this->configuration = $configuration;
            }
            return $succes;
        }
        else
        {
            return ! $duplicate;
        }
    }

    private function create_answers($values)
    {
        $answerService = $this->parent->getApplicationConfiguration()->getAnswerService();
        $answerIds = $this->complex_question->getAnswerIds($answerService->getPrefix());
        $answers = array();

        foreach ($answerIds as $answerId)
        {
            $answer = $values[$answerId];
            if ($answer)
            {
                $ids = explode('_', $answerId);
                $reverseIds = array_reverse($ids);
                array_pop($reverseIds);
                $ids = array_reverse($reverseIds);
                $answerId = implode('_', $ids);
                $answers[$answerId] = $answer;
            }
        }
        return $answers;
    }

    private function isDuplicate(Configuration $configuration)
    {
        $duplicate = false;
        $configs = $this->page->getconfiguration();

        foreach ($configs as $conf)
        {
            $answer_diff = array_diff_assoc($configuration->getAnswerMatches(), $conf->getAnswerMatches());
            $same_from_id = $configuration->getComplexQuestionId() == $conf->getComplexQuestionId();
            $to_ids_diff = array_diff($configuration->getToVisibleQuestionIds(), $conf->getToVisibleQuestionIds());

            if ($same_from_id && count($answer_diff) == 0 && count($to_ids_diff) == 0)
            {
                return true;
            }
        }
        return $duplicate;
    }

    /**
     * Sets default values.
     *
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        if ($this->config_id)
        {
            $configuration = $this->getConfiguration();
            $defaults[Configuration :: PROPERTY_NAME] = $configuration->getName();
            $defaults[Configuration :: PROPERTY_DESCRIPTION] = $configuration->getDescription();

            foreach ($configuration->getToVisibleQuestionIds() as $id)
            {
                $defaults[self :: TO_VISIBLE_QUESTION_ID . '_' . $id] = 1;
            }

            parent :: setDefaults($defaults);
        }
    }

    function get_to_visible_question_ids($values)
    {
        $keys = array_keys($values);
        $complex_question_ids = array();

        $complex_content_object_path = $this->page->get_complex_content_object_path();
        $nodes = $complex_content_object_path->get_nodes();

        foreach ($nodes as $node)
        {
            if (! $node->is_root())
            {
                $complex_content_object_item = $node->get_complex_content_object_item();

                $id = $complex_content_object_item->get_id();
                $key = self :: TO_VISIBLE_QUESTION_ID . '_' . $id;
                if (in_array($key, $keys))
                {
                    if ($values[$key] == 1)
                    {
                        $complex_question_ids[] = $id;
                    }
                }
            }
        }
        return $complex_question_ids;
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }
}
?>