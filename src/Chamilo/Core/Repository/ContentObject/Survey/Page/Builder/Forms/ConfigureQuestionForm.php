<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Forms;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Description\Storage\DataClass\Description;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Manager;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass\PageConfig;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

class ConfigureQuestionForm extends FormValidator
{
    const FORM_NAME = 'survey_builder_configure_question_form';
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'QuestionConfigurationUpdated';
    const RESULT_ERROR = 'QuestionConfigurationUpdateFailed';
    const TO_VISIBLE_QUESTION_ID = 'to_visible_question_id';

    private $parent;

    private $survey_page;

    private $complex_question;

    private $complex_question_id;

    private $config_index;

    private $answer;

    private $complex_content_object_path_node;

    function __construct($parent, $config_index)
    {
        parent :: __construct(self :: FORM_NAME, self :: FORM_METHOD_POST, $parent->get_url());
        
        $this->parent = $parent;
        
        $this->survey_page = $this->parent->get_root_content_object();
        
        $this->complex_question_id = Request :: get(Manager :: PARAM_COMPLEX_QUESTION_ITEM_ID);
        
        $complex_content_object_path = $this->survey_page->get_complex_content_object_path();
        $nodes = $complex_content_object_path->get_nodes();
        
        $complex_content_object_path_node = null;
        foreach ($nodes as $node)
        {
            
            if (! $node->is_root())
            {
                $id = $node->get_complex_content_object_item()->get_id();
                
                if ($id == $this->complex_question_id)
                {
                    $this->complex_content_object_path_node = $node;
                    break;
                }
            }
        }
        
        $this->complex_question = $this->complex_content_object_path_node->get_complex_content_object_item();
        
        if ($config_index)
        {
            $this->form_type = self :: TYPE_EDIT;
        }
        else
        {
            $this->form_type = self :: TYPE_CREATE;
        }
        
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->config_index = $config_index;
            $configs = $this->survey_page->get_config();
            $this->answer = $configs[$this->config_index][PageConfig :: PROPERTY_ANSWER_MATCHES];
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
        $this->addElement('text', PageConfig :: PROPERTY_NAME, Translation :: get('Name'), array("size" => "50"));
        $this->addRule(
            PageConfig :: PROPERTY_NAME, 
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 
            'required');
        
        $this->add_html_editor(PageConfig :: PROPERTY_DESCRIPTION, Translation :: get('Description'), false);
        
        $this->addElement(
            'html', 
            '<div class="row"><div class="label"></div><div class="formw"><div class="element"  style="position : relative ; width : 100%" >');
        
        $question_display = \Chamilo\Core\Repository\ContentObject\Survey\Page\Display\QuestionDisplay :: factory(
            $this, 
            $this->complex_content_object_path_node);
        
        // $question_display = \repository\content_object\survey_page\display\QuestionDisplay :: factory($this,
        // $this->complex_question, $this->answer);
        $question_display->run();
        
        $this->addElement('html', '</div></div><div class="clear">&nbsp;</div></div>');
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem :: class_name(), 
                ComplexContentObjectItem :: PROPERTY_PARENT), 
            new StaticConditionVariable($this->survey_page->get_id()));
        $order = new OrderBy(
            new PropertyConditionVariable(
                ComplexContentObjectItem :: class_name(), 
                ComplexContentObjectItem :: PROPERTY_DISPLAY_ORDER));
        $complex_questions = \Chamilo\Core\Repository\Storage\DataManager :: retrieves(
            ComplexContentObjectItem :: class_name(), 
            new DataClassRetrievesParameters($condition, null, null, array($order)));
        $sub_nr = 0;
        
        while ($complex_question = $complex_questions->next_result())
        {
            if (! $complex_question->is_visible())
            {
                
                $complex_id = $complex_question->get_id();
                
                $checkbox = $this->createElement(
                    'checkbox', 
                    self :: TO_VISIBLE_QUESTION_ID . '_' . $complex_id, 
                    Translation :: get('MakeVisible'), 
                    '', 
                    array());
                $this->addElement($checkbox);
                
                $question = $complex_question->get_ref_object();
                $question_rendition = ContentObjectRenditionImplementation :: factory(
                    $question, 
                    ContentObjectRendition :: FORMAT_HTML, 
                    ContentObjectRendition :: VIEW_FULL, 
                    $this);
                if (! $question instanceof Description)
                {
                    $sub_nr ++;
                }
                
                $html = array();
                $html[] = '<div class="row"><div class="label"></div><div class="formw"><div class="element"  style="position : relative ; width : 100%" >';
                $html[] = $question_rendition->get_question_preview('1.' . $sub_nr, $complex_id);
                $html[] = '</div></div><div class="clear">&nbsp;</div></div>';
                $this->addElement('html', implode(PHP_EOL, $html));
            }
        }
    }

    function build_editing_form()
    {
        $this->build_basic_form();
        $this->addElement('hidden', PageConfig :: PROPERTY_CONFIG_CREATED);
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation :: get('Update'), 
            array('class' => 'positive update'));
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation :: get('Reset'), 
            array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->addElement('category');
    }

    function build_creation_form()
    {
        $this->build_basic_form();
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES), 
            array('class' => 'positive'));
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), 
            array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->addElement('category');
    }

    function update_config()
    {
        $values = $this->exportValues();
        
        $configs = $this->survey_page->get_config();
        $config = $configs[$this->config_index];
        
        $config[PageConfig :: PROPERTY_FROM_VISIBLE_QUESTION_ID] = $this->complex_question->get_id();
        $config[PageConfig :: PROPERTY_TO_VISIBLE_QUESTION_IDS] = $this->get_to_visible_question_ids($values);
        $config[PageConfig :: PROPERTY_ANSWER_MATCHES] = $this->create_answers($values);
        $duplicate = $this->is_duplicate($config);
        
        if (! $duplicate)
        {
            $config[PageConfig :: PROPERTY_NAME] = $values[PageConfig :: PROPERTY_NAME];
            $config[PageConfig :: PROPERTY_DESCRIPTION] = $values[PageConfig :: PROPERTY_DESCRIPTION];
            $config[PageConfig :: PROPERTY_CONFIG_UPDATED] = time();
            $configs[$this->config_index] = $config;
            $this->survey_page->set_config($configs);
            return $this->survey_page->update();
        }
        else
        {
            $configs = $this->survey_page->get_config();
            $config = $configs[$this->config_index];
            $config[PageConfig :: PROPERTY_NAME] = $values[PageConfig :: PROPERTY_NAME];
            $config[PageConfig :: PROPERTY_DESCRIPTION] = $values[PageConfig :: PROPERTY_DESCRIPTION];
            $config[PageConfig :: PROPERTY_CONFIG_UPDATED] = time();
            $configs[$this->config_index] = $config;
            $this->survey_page->set_config($configs);
            return $this->survey_page->update();
        }
    }

    function create_config()
    {
        $values = $this->exportValues();
        
        $config = array();
        $config[PageConfig :: PROPERTY_NAME] = $values[PageConfig :: PROPERTY_NAME];
        $config[PageConfig :: PROPERTY_DESCRIPTION] = $values[PageConfig :: PROPERTY_DESCRIPTION];
        $config[PageConfig :: PROPERTY_FROM_VISIBLE_QUESTION_ID] = $this->complex_question->get_id();
        $config[PageConfig :: PROPERTY_TO_VISIBLE_QUESTION_IDS] = $this->get_to_visible_question_ids($values);
        $config[PageConfig :: PROPERTY_ANSWER_MATCHES] = $this->create_answers($values);
        
        $duplicate = $this->is_duplicate($config);
        
        if (! $duplicate)
        {
            $index = time();
            $config[PageConfig :: PROPERTY_CONFIG_CREATED] = $index;
            $config[PageConfig :: PROPERTY_CONFIG_UPDATED] = $index;
            $configs = $this->survey_page->get_config();
            $configs[$index] = $config;
            $this->survey_page->set_config($configs);
            return $this->survey_page->update();
        }
        else
        {
            return ! $duplicate;
        }
    }

    private function create_answers($values)
    {
        $keys = array_keys($values);
        $answers = array();
        foreach ($keys as $key)
        {
            $ids = explode('_', $key);
            if ($ids[0] == $this->complex_question->get_id())
            {
                $answers[$key] = $values[$key];
            }
        }
        return $answers;
    }

    private function is_duplicate($config)
    {
        $duplicate = false;
        $configs = $this->survey_page->get_config();
        
        foreach ($configs as $conf)
        {
            $answer_diff = array_diff(
                $config[PageConfig :: PROPERTY_ANSWER_MATCHES], 
                $conf[PageConfig :: PROPERTY_ANSWER_MATCHES]);
            $same_from_id = $config[PageConfig :: PROPERTY_FROM_VISIBLE_QUESTION_ID] ==
                 $conf[PageConfig :: PROPERTY_FROM_VISIBLE_QUESTION_ID];
            $to_ids_diff = array_diff(
                $config[PageConfig :: PROPERTY_TO_VISIBLE_QUESTION_IDS], 
                $conf[PageConfig :: PROPERTY_TO_VISIBLE_QUESTION_IDS]);
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
        if ($this->config_index)
        {
            $configs = $this->survey_page->get_config();
            $config = $configs[$this->config_index];
            $defaults[PageConfig :: PROPERTY_NAME] = $config[PageConfig :: PROPERTY_NAME];
            $defaults[PageConfig :: PROPERTY_DESCRIPTION] = $config[PageConfig :: PROPERTY_DESCRIPTION];
            
            foreach ($config[PageConfig :: PROPERTY_TO_VISIBLE_QUESTION_IDS] as $id)
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
        
        $complex_content_object_path = $this->survey_page->get_complex_content_object_path();
        $nodes = $complex_content_object_path->get_nodes();
        
        foreach ($nodes as $node)
        {
            
            if (! $node->is_root())
            {
                $complex_content_object_item = $node->get_complex_content_object_item();
                
                if (! $complex_content_object_item->is_visible())
                {
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
        }
        
        return $complex_question_ids;
    }
}
?>