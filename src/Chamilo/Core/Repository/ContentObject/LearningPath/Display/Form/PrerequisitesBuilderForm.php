<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Form;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component\PrerequisitesBuilderComponent;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\String\Text;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package core\repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PrerequisitesBuilderForm extends FormValidator
{
    const FORM_TYPE_ADVANCED = 'advanced';
    const FORM_TYPE_BASIC = 'basic';
    const IDENTIFER = 'clpi_';

    /**
     *
     * @var PrerequisitesBuilderComponent
     */
    private $component;

    private $user;

    private $clo_item;

    private $form_type;

    private $clpi_id;

    public function __construct(PrerequisitesBuilderComponent $component)
    {
        parent :: __construct('prerequisites', 'post', $component->get_url());

        $this->component = $component;
        $this->user = $component->get_user();
        $this->clo_item = $component->get_current_complex_content_object_item();
        $this->clpi_id = $this->clo_item->get_id();

        $this->setDefaults();
        $this->handle_session_values();

        switch ($this->form_type)
        {
            case self :: FORM_TYPE_ADVANCED :
                $this->build_advanced_form();
                break;
            case self :: FORM_TYPE_BASIC :
                $this->build_basic_form();
                break;
        }
    }

    public function set_form_type($form_type)
    {
        $_SESSION[self :: IDENTIFER . $this->clpi_id]['form_type'] = $form_type;
    }

    public function handle_session_values()
    {
        $clpi_id = $this->clpi_id;

        if (! $this->isSubmitted())
        {
            unset($_SESSION[self :: IDENTIFER . $clpi_id]['number_of_groups']);
            unset($_SESSION[self :: IDENTIFER . $clpi_id]['number_of_items']);
            unset($_SESSION[self :: IDENTIFER . $clpi_id]['skip_items']);
            unset($_SESSION[self :: IDENTIFER . $clpi_id]['skip_groups']);
        }

        if (isset($_POST['go_basic']))
        {
            $_SESSION[self :: IDENTIFER . $clpi_id]['form_type'] = self :: FORM_TYPE_BASIC;
        }
        else
            if (isset($_POST['go_advanced']))
            {
                $_SESSION[self :: IDENTIFER . $clpi_id]['form_type'] = self :: FORM_TYPE_ADVANCED;
            }

        if (isset($_POST['add_group']))
        {
            $_SESSION[self :: IDENTIFER . $clpi_id]['number_of_groups'] = $_SESSION[self :: IDENTIFER . $clpi_id]['number_of_groups'] +
                 1;

            $group_number = $_SESSION[self :: IDENTIFER . $clpi_id]['number_of_groups'] - 1;

            $_SESSION[self :: IDENTIFER . $clpi_id]['number_of_items'][$group_number] = 2;

            $_SESSION[self :: IDENTIFER . $clpi_id]['skip_items'][$group_number] = array();
        }

        if (isset($_POST['remove_group']))
        {
            $indexes = array_keys($_POST['remove_group']);

            $_SESSION[self :: IDENTIFER . $clpi_id]['skip_groups'][] = $indexes[0];
            unset($_SESSION[self :: IDENTIFER . $clpi_id]['number_of_items'][$indexes[0]]);
        }

        if (isset($_POST['add_item']))
        {
            $indexes = array_keys($_POST['add_item']);
            $_SESSION[self :: IDENTIFER . $clpi_id]['number_of_items'][$indexes[0]] ++;
        }

        if (isset($_POST['remove_item']))
        {
            foreach ($_POST['remove_item'] as $group_number => $item)
            {
                $indexes = array_keys($item);
                $_SESSION[self :: IDENTIFER . $clpi_id]['skip_items'][$group_number][] = $indexes[0];
            }
        }

        if (isset($_POST['submit_basic']))
        {
            $this->form_type = self :: FORM_TYPE_BASIC;
        }

        if ($this->number_of_groups)
        {
            $_SESSION[self :: IDENTIFER . $clpi_id]['number_of_groups'] = $this->number_of_groups;
        }

        if ($this->number_of_items)
        {
            $_SESSION[self :: IDENTIFER . $clpi_id]['number_of_items'] = $this->number_of_items;
        }

        if (! isset($_SESSION[self :: IDENTIFER . $clpi_id]['number_of_groups']))
        {
            $_SESSION[self :: IDENTIFER . $clpi_id]['number_of_groups'] = 1;
            $_SESSION[self :: IDENTIFER . $clpi_id]['number_of_items'][0] = 2;
        }

        if (! isset($_SESSION[self :: IDENTIFER . $clpi_id]['skip_groups']))
        {
            $_SESSION[self :: IDENTIFER . $clpi_id]['skip_groups'] = array();
            $_SESSION[self :: IDENTIFER . $clpi_id]['skip_items'][] = array();
        }

        $this->form_type = $_SESSION[self :: IDENTIFER . $clpi_id]['form_type'];
    }

    /*
     * This method shows basic prerequisites builder
     */
    public function build_basic_form()
    {
        $prerequisite = 'basic_prerequisite';
        $choices = array();

        $choices[] = $this->createElement('radio', $prerequisite, '', Translation :: get('NoPrerequisites'), - 1);

        foreach ($this->component->get_current_node()->get_siblings() as $sibling)
        {
            $choices[] = $this->createElement(
                'radio',
                $prerequisite,
                '',
                $sibling->get_content_object()->get_title(),
                $sibling->get_complex_content_object_item()->get_id());
        }

        $this->addGroup($choices, $prerequisite, Translation :: get('Steps'), '', false);
        $form_buttons = array();
        $form_buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES));
        $form_buttons[] = $this->createElement(
            'style_submit_button',
            'submit_basic',
            Translation :: get('SavePrerequisites'),
            null,
            null,
            'floppy-save');
        $form_buttons[] = $this->createElement(
            'style_button',
            'go_advanced',
            Translation :: get('AdvancedPrerequisites'),
            null,
            null,
            'chevron-right');

        $this->addGroup($form_buttons, 'option_buttons', null, '&nbsp;', false);
    }

    public function build_advanced_form()
    {
        $clpi_id = $this->clpi_id;

        $renderer = &$this->defaultRenderer();

        $operator = array(
            '' => Translation :: get('Operator'),
            '&' => Translation :: get('And', null, Utilities :: COMMON_LIBRARIES),
            '|' => Translation :: get('Or', null, Utilities :: COMMON_LIBRARIES));
        $goperator = array(
            '&' => Translation :: get('And', null, Utilities :: COMMON_LIBRARIES),
            '|' => Translation :: get('Or', null, Utilities :: COMMON_LIBRARIES));

        $not = array('' => '', '~' => Translation :: get('Not', null, Utilities :: COMMON_LIBRARIES));
        $siblings = $this->component->get_current_node()->get_siblings();

        $sibling_options = array();
        $sibling_options[- 1] = Translation :: get('NoPrerequisites');
        foreach ($siblings as $sibling)
        {
            $sibling_options[$sibling->get_complex_content_object_item()->get_id()] = $sibling->get_content_object()->get_title();
        }

        $number_of_groups = $_SESSION[self :: IDENTIFER . $clpi_id]['number_of_groups'];
        $gcounter = 0;
        for ($group_number = 0; $group_number < $number_of_groups; $group_number ++)
        {
            if (! in_array($group_number, $_SESSION[self :: IDENTIFER . $clpi_id]['skip_groups']))
            {

                $category_html = array();
                $category_html[] = '<div class="prerequisite_group">';
                $category_html[] = '<div class="header">';
                $category_html[] = '<div class="operator">';
                $this->addElement('html', implode(PHP_EOL, $category_html));

                if ($gcounter > 0 && $group_number != 0)
                {
                    $renderer->setElementTemplate('{element}', 'group_operator[' . $group_number . ']');
                    $this->addElement('select', 'group_operator[' . $group_number . ']', '', $goperator);
                }

                $category_html = array();
                $category_html[] = '</div>';
                $category_html[] = '<div class="title">' . Translation :: get('PrerequisiteGroup') . ' ' .
                     ($gcounter + 1) . '</div>';
                $category_html[] = '<div class="actions">';
                $this->addElement('html', implode(PHP_EOL, $category_html));

                if ($_SESSION[self :: IDENTIFER . $clpi_id]['number_of_groups'] - count(
                    $_SESSION[self :: IDENTIFER . $clpi_id]['skip_groups']) > 1)
                {

                    $renderer->setElementTemplate('{element}', 'remove_group[' . $group_number . ']');
                    $group[] = $this->addElement(
                        'image',
                        'remove_group[' . $group_number . ']',
                        Theme :: getInstance()->getCommonImagePath('Action/Delete'),
                        array(
                            'title' => Translation :: get('RemoveGroup'),
                            'class' => 'remove_group',
                            'id' => $group_number));
                }

                $category_html = array();
                $category_html[] = '</div>';
                $category_html[] = '</div>';
                $category_html[] = '<div class="body">';
                $this->addElement('html', implode(PHP_EOL, $category_html));

                $number_of_items = intval($_SESSION[self :: IDENTIFER . $clpi_id]['number_of_items'][$group_number]);

                $counter = 0;
                for ($item_number = 0; $item_number < $number_of_items; $item_number ++)
                {
                    if (! in_array($item_number, $_SESSION[self :: IDENTIFER . $clpi_id]['skip_items'][$group_number]))
                    {
                        $identifier = '[' . $group_number . '][' . $item_number . ']';
                        $group = array();

                        if ($counter > 0)
                        {
                            $group[] = $this->createElement('select', 'operator' . $identifier, '', $operator);
                        }
                        else
                        {
                            $element = $this->createElement(
                                'select',
                                'operator' . $identifier,
                                '',
                                $operator,
                                array('disabled'));
                            $group[] = $element;
                        }

                        $group[] = $this->createElement('select', 'not' . $identifier, '', $not);
                        $group[] = $this->createElement('select', 'prerequisite' . $identifier, '', $sibling_options);

                        if ($_SESSION[self :: IDENTIFER . $clpi_id]['number_of_items'][$group_number] - count(
                            $_SESSION[self :: IDENTIFER . $clpi_id]['skip_items'][$group_number]) > 1)
                        {

                            $group[] = & $this->createElement(
                                'image',
                                'remove_item[' . $group_number . '][' . $item_number . ']',
                                Theme :: getInstance()->getCommonImagePath('Action/Delete'),
                                array(
                                    'title' => Translation :: get('RemoveItem'),
                                    'class' => 'remove_item',
                                    'id' => $group_number . '_' . $item_number));
                        }

                        $this->addGroup($group, 'item_' . $group_number . '_' . $item_number, null, '', false);
                        $renderer->setGroupElementTemplate(
                            '{element} &nbsp; ',
                            'item_' . $group_number . '_' . $item_number);

                        $counter ++;
                    }
                }

                $gcounter ++;

                $renderer->setElementTemplate('{element}', 'add_item[' . $group_number . ']');
                $this->addElement('html', '<div style="border-top: 1px dotted #cecece; padding: 10px;">');
                $this->addElement(
                    'image',
                    'add_item[' . $group_number . ']',
                    Theme :: getInstance()->getCommonImagePath('Action/Add'),
                    array(
                        'title' => Translation :: get('Add', null, Utilities :: COMMON_LIBRARIES),
                        'class' => 'add_item',
                        'id' => $group_number));
                $this->addElement('html', '</div>');

                $category_html = array();
                $category_html[] = '<div style="clear: both;"></div>';
                $category_html[] = '</div>';
                $category_html[] = '<div style="clear: both;"></div>';
                $category_html[] = '</div>';
                $this->addElement('html', implode(PHP_EOL, $category_html));
            }
        }

        $form_buttons = array();

        // check if the item has already the prerequisites

        $prerequisites = $this->clo_item->get_prerequisites();
        $form_buttons[] = $this->createElement(
            'style_button',
            'go_basic',
            Translation :: get('BasicPrerequisites'),
            null,
            null,
            'chevron-left');
        $form_buttons[] = $this->createElement(
            'style_button',
            'add_group[]',
            Translation :: get('AddPrerequisiteGroup'),
            array('id' => 'add_group'),
            null,
            'plus');
        $form_buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation :: get('SavePrerequisites'),
            null,
            null,
            'floppy-save');
        $this->addGroup($form_buttons, 'option_buttons', null, '&nbsp;', false);
    }

    public function validate()
    {
        if (isset($_POST['submit']) || isset($_POST['submit_basic']))
        {
            return parent :: validate();
        }

        return false;
    }

    private $number_of_groups;

    private $number_of_items;

    public function setDefaults($defaults = array())
    {
        $prerequisites = $this->clo_item->get_prerequisites();
        $defaults_basic = '';

        // When the form is accessed for the first time and there are no prerequisites set
        if (sizeof($_POST) == 0)
        {
            if (! $prerequisites)
            {
                $this->set_form_type(self :: FORM_TYPE_BASIC);
            }
        }

        if (($prerequisites && ! $this->isSubmitted()) || ($prerequisites && isset($_POST['go_advanced'])) || ($prerequisites && isset(
            $_POST['go_basic'])))
        {

            $pattern = '/\([^\)]*\)/';
            $matches = array();
            preg_match_all($pattern, $prerequisites, $matches);
            $groups = $matches[0];

            foreach ($groups as $i => $group)
            {
                $prerequisites = str_replace($group, '_', $prerequisites);
                $group = str_replace('(', '', $group);
                $group = str_replace(')', '', $group);

                $or_values = explode('|', $group);

                $item_counter = 0;
                foreach ($or_values as $or_value)
                {
                    if (strpos($or_value, '&') === false)
                    {
                        if (Text :: char_at($or_value, 0) == '~')
                        {
                            $or_value = substr($or_value, 1);
                            $defaults['not'][$i][$item_counter] = '~';
                        }

                        $defaults['prerequisite'][$i][$item_counter] = $or_value;
                        if ($item_counter > 0)
                            $defaults['operator'][$i][$item_counter] = '|';

                        $item_counter ++;
                        continue;
                    }

                    $and_values = explode('&', $or_value);
                    foreach ($and_values as $and_value)
                    {
                        if (Text :: char_at($and_value, 0) == '~')
                        {
                            $and_value = substr($and_value, 1);
                            $defaults['not'][$i][$item_counter] = '~';
                        }
                        $defaults['prerequisite'][$i][$item_counter] = $and_value;
                        $defaults_basic = $and_value;
                        if ($item_counter > 0)
                            $defaults['operator'][$i][$item_counter] = '&';
                        $item_counter ++;
                    }
                }

                $this->number_of_items[$i] = $item_counter;
            }

            $this->number_of_groups = count($groups);

            $operators = explode('_', $prerequisites);

            $defaults['group_operator'] = $operators;

            if ($this->number_of_groups == 0 && is_numeric($prerequisites))
            {
                $this->number_of_groups = 1;
                $this->number_of_items[0] = 1;
                $defaults['prerequisite'][0][0] = $prerequisites;
            }

            if ($this->number_of_groups > 1 || $item_counter > 2)
            {
                $this->set_form_type(self :: FORM_TYPE_ADVANCED);
            }
            else
                $this->set_form_type(self :: FORM_TYPE_BASIC);
        }

        $defaults['basic_prerequisite'] = $defaults_basic;
        parent :: setDefaults($defaults);
    }

    public function build_basic_prerequisites()
    {
        $values = $this->exportValues();
        $prereq_formula = '';
        $prerequisites_string = '';
        if ($values['basic_prerequisite'] == - 1)
        {
            $prereq_formula = '';
        }
        else
        {
            $prerequisites_string .= '&' . $values['basic_prerequisite'];
            if ($prerequisites_string != '')
            {
                $prereq_formula .= '(';
                $prereq_formula .= $prerequisites_string;
                $prereq_formula .= ')';
            }
        }
        $this->clo_item->set_prerequisites($prereq_formula);
    }

    public function build_prerequisites()
    {
        if ($this->form_type == self :: FORM_TYPE_BASIC)
        {
            $this->build_basic_prerequisites();
        }
        else
        {
            $values = $this->exportValues();
            $prereq_formula = '';

            foreach ($values['prerequisite'] as $group_number => $items)
            {
                $prerequisites_string = '';
                foreach ($items as $item_number => $item)
                {
                    if ($item == - 1)
                    {
                        continue;
                    }
                    $prerequisites_string .= $values['operator'][$group_number][$item_number] .
                         $values['not'][$group_number][$item_number] . $item;
                }
                if (strlen($prerequisites_string) > 1)
                {
                    $prereq_formula .= $values['group_operator'][$group_number] . '(';
                    $prereq_formula .= $prerequisites_string;
                    $prereq_formula .= ')';
                }
            }
            $this->clo_item->set_prerequisites($prereq_formula);
        }

        unset($_SESSION[self :: IDENTIFER . $this->clpi_id]);
        return $this->clo_item->update();
    }
}
