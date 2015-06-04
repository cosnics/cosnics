<?php
namespace Chamilo\Core\MetadataOld\Value\Form;

use Chamilo\Core\MetadataOld\Attribute\Storage\DataClass\Attribute;
use Chamilo\Core\MetadataOld\Element\Storage\DataClass\Element;
use Chamilo\Core\MetadataOld\FixedElementsProvider;
use Chamilo\Core\MetadataOld\Schema\Storage\DataClass\Schema;
use Chamilo\Core\MetadataOld\Value\Storage\DataManager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Tabs\DynamicFormTab;
use Chamilo\Libraries\Format\Tabs\DynamicFormTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * This form builder creates all controls necessary to edit metadata of a generic object.
 * 
 * @package core\metadata\value
 * @author Tom Goethals - Hogeschool Gent
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ValueEditorFormBuilder
{
    const FORM_ELEMENT_METADATA_PREFIX = 'md';
    const FORM_ELEMENT_VALUE = 'value';

    /**
     * The FormValidator reference
     * 
     * @var \libraries\format\FormValidator
     */
    private $form;

    /**
     * The fixed elements provider
     * 
     * @var FixedElementsProvider
     */
    private $fixed_elements_provider;

    /**
     * Constructor
     * 
     * @param FormValidator $form
     * @param FixedElementsProvider $fixed_elements_provider
     */
    public function __construct(FormValidator $form = null, FixedElementsProvider $fixed_elements_provider = null)
    {
        if (! $form)
        {
            $form = new FormValidator('metadata_form');
        }
        
        $this->set_fixed_elements_provider($fixed_elements_provider);
        
        $this->form = $form;
    }

    /**
     * Builds the entire form
     * 
     * @param ElementValue[] $default_element_values
     */
    public function build_form($default_element_values = array())
    {
        $this->form->addElement(
            'html', 
            ResourceManager :: get_instance()->get_resource_html(
                Theme :: getInstance()->getStylesheetPath(__NAMESPACE__, true)));
        
        $this->add_schemas();
        
        if (count($default_element_values))
        {
            $this->set_default_values($default_element_values);
        }
    }

    /**
     * Sets the fixed element provider
     * 
     * @param \Chamilo\Core\MetadataOld\FixedElementsProvider $fixed_elements_provider
     *
     * @throws \InvalidArgumentException
     */
    public function set_fixed_elements_provider(FixedElementsProvider $fixed_elements_provider = null)
    {
        if ($fixed_elements_provider && ! $fixed_elements_provider instanceof FixedElementsProvider)
        {
            throw new \InvalidArgumentException(
                'The given fixed_elements_provider must be an instance of FixedElementsProvider');
        }
        
        $this->fixed_elements_provider = $fixed_elements_provider;
    }

    /**
     * Adds the submit buttons to the current form
     */
    public function add_submit_buttons()
    {
        $form = $this->form;
        
        $buttons = array();
        
        $buttons[] = $form->createElement(
            'style_submit_button', 
            'submit_button', 
            Translation :: get('Save', null, Utilities :: COMMON_LIBRARIES), 
            array('class' => 'positive'));
        
        $buttons[] = $form->createElement(
            'style_reset_button', 
            'reset', 
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), 
            array('class' => 'normal empty'));
        
        $form->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Adds the schemas to the form
     */
    protected function add_schemas()
    {
        $form = $this->form;
        
        $tabs_renderer = new DynamicFormTabsRenderer('metadata_schemas', $form);
        
        $schemas = \Chamilo\Core\MetadataOld\Schema\Storage\DataManager :: retrieves(Schema :: class_name());
        while ($schema = $schemas->next_result())
        {
            if (\Chamilo\Core\MetadataOld\Element\Storage\DataManager :: count_elements_for_schema($schema->get_id()) == 0)
            {
                continue;
            }
            
            $tabs_renderer->add_tab(
                new DynamicFormTab(
                    $schema->get_id(), 
                    $schema->get_name(), 
                    null, 
                    array($this, 'add_elements_from_schema'), 
                    array($schema)));
        }
        
        $tabs_renderer->render();
    }

    /**
     * Adds the elements from the given schema to the form
     * 
     * @param Schema $schema
     */
    public function add_elements_from_schema(Schema $schema)
    {
        $elements = \Chamilo\Core\MetadataOld\Element\Storage\DataManager :: retrieve_parent_elements_from_schema(
            $schema->get_id());
        
        $fixed_elements = array();
        $dynamic_elements = array();
        $nested_elements = array();
        
        while ($element = $elements->next_result())
        {
            if ($this->is_fixed_element($element))
            {
                $fixed_elements[] = $element;
            }
            else
            {
                if (count($element->get_nested_elements()) > 0)
                {
                    $nested_elements[] = $element;
                }
                else
                {
                    $dynamic_elements[] = $element;
                }
            }
        }
        
        if (count($fixed_elements))
        {
            $this->add_fixed_elements($fixed_elements);
        }
        
        if (count($dynamic_elements))
        {
            $this->form->addElement('category', Translation :: get('Elements'));
            $this->add_dynamic_elements($dynamic_elements, $schema);
            $this->form->addElement('category');
        }
        
        if (count($nested_elements))
        {
            $this->add_nested_elements($nested_elements, $schema);
        }
    }

    /**
     * Adds the fixed elements to the form
     * 
     * @param array $fixed_elements
     */
    protected function add_fixed_elements(array $fixed_elements = array())
    {
        $form = $this->form;
        
        $form->addElement('category', Translation :: get('FixedElements'));
        
        foreach ($fixed_elements as $element)
        {
            $form->addElement(
                'static', 
                $element->get_id(), 
                $element->get_display_name(), 
                $this->get_fixed_element_value($element));
        }
        
        $form->addElement('category');
    }

    /**
     * Returns whether or not the element is fixed
     * 
     * @param Element $element
     *
     * @return bool
     */
    protected function is_fixed_element($element)
    {
        if (! $this->fixed_elements_provider)
        {
            return false;
        }
        
        $fixed_elements = $this->fixed_elements_provider->get_fixed_elements();
        if ($fixed_elements)
        {
            return array_key_exists($element->get_id(), $fixed_elements);
        }
        
        return false;
    }

    /**
     * Returns the fixed value (if any) for the given element
     * 
     * @param Element $element
     *
     * @return string
     */
    protected function get_fixed_element_value($element)
    {
        if (! $this->fixed_elements_provider)
        {
            return null;
        }
        
        $fixed_elements = $this->fixed_elements_provider->get_fixed_elements();
        if ($fixed_elements)
        {
            if (array_key_exists($element->get_id(), $fixed_elements))
            {
                return $fixed_elements[$element->get_id()];
            }
        }
        
        return null;
    }

    /**
     * Adds the dynamic form elements
     * 
     * @param Element[] $elements
     * @param Schema $schema
     */
    protected function add_dynamic_elements(array $elements, Schema $schema)
    {
        $form = $this->form;
        $namespace = $schema->get_namespace();
        
        foreach ($elements as $element)
        {
            $form->addElement(
                'html', 
                '<table id="' . $schema->get_namespace() . ':' . $element->get_name() . '" class="metadata_table">');
            
            $group_name = self :: FORM_ELEMENT_METADATA_PREFIX . '$' . $namespace . ':' . $element->get_name();
            
            $group_elements = array();
            
            $display_name = $element->get_display_name();
            
            if (\Chamilo\Core\MetadataOld\Element\Storage\DataManager :: element_has_controlled_vocabulary(
                $element->get_id()))
            {
                $vocabulary = \Chamilo\Core\MetadataOld\Element\Storage\DataManager :: retrieve_controlled_vocabulary_terms_from_element(
                    $element->get_id());
                
                $group_elements[] = $form->createElement(
                    'select', 
                    self :: FORM_ELEMENT_VALUE, 
                    $display_name, 
                    $vocabulary);
            }
            else
            {
                $group_elements[] = $form->createElement(
                    'text', 
                    self :: FORM_ELEMENT_VALUE, 
                    $display_name, 
                    array('size' => 50));
            }
            
            $this->add_attributes_for_element($group_elements, $element);
            
            $form->addGroup($group_elements, $group_name, '', null, true);
            
            $renderer = $form->defaultRenderer();
            
            $renderer->setElementTemplate('<tr class="element_row">{element}</tr>', $group_name);
            
            $renderer->setGroupElementTemplate(
                '<!-- BEGIN label --><td class="label">{label}</td><!-- END label -->' .
                     '<td class="element">{element}</td>', 
                    $group_name);
            
            $form->addElement('html', '</table>');
        }
    }

    /**
     * Adds the elements that have children attached to them for a given schema
     * 
     * @param array $elements
     * @param \Chamilo\Core\MetadataOld\schema\storage\data_class\Schema $schema
     */
    protected function add_nested_elements(array $elements, Schema $schema)
    {
        $form = $this->form;
        
        foreach ($elements as $element)
        {
            $form->addElement('category', $element->get_display_name());
            
            $dynamic_elements = array();
            $child_nested_elements = array();
            
            $nested_elements = $element->get_nested_elements();
            foreach ($nested_elements as $nested_element)
            {
                if (count($nested_element->get_nested_elements()) > 0)
                {
                    $child_nested_elements[] = $nested_element;
                }
                else
                {
                    $dynamic_elements[] = $nested_element;
                }
            }
            
            if (count($dynamic_elements))
            {
                $this->add_dynamic_elements($dynamic_elements, $schema);
            }
            
            if (count($child_nested_elements))
            {
                $this->add_nested_elements($child_nested_elements, $schema);
            }
            
            $form->addElement('category');
        }
    }

    /**
     * Adds the controls for attributes of an element to the given group
     * 
     * @param array $group_elements
     * @param Element $element the element
     */
    protected function add_attributes_for_element(array &$group_elements, Element $element)
    {
        $form = $this->form;
        
        $attributes = \Chamilo\Core\MetadataOld\Attribute\Storage\DataManager :: retrieve_attributes_for_element(
            $element->get_id());
        
        while ($attribute = $attributes->next_result())
        {
            $form_element_name = $attribute->get_namespace() . ':' . $attribute->get_name();
            $display_name = $attribute->get_display_name();
            
            if (\Chamilo\Core\MetadataOld\Storage\DataManager :: attribute_has_controlled_vocabulary($attribute->get_id()))
            {
                $vocabulary = \Chamilo\Core\MetadataOld\Attribute\Storage\DataManager :: retrieve_controlled_vocabulary_terms_from_attribute(
                    $attribute->get_id());
                
                $group_elements[] = $form->createElement('select', $form_element_name, $display_name, $vocabulary);
            }
            else
            {
                $group_elements[] = $form->createElement('text', $form_element_name, $display_name);
            }
        }
    }

    /**
     * Sets the default values with the given element values
     * 
     * @param ElementValue[] $element_values
     */
    protected function set_default_values(array $element_values)
    {
        $default_values = array();
        
        foreach ($element_values as $element_value)
        {
            $element = DataManager :: retrieve_by_id(Element :: class_name(), $element_value->get_element_id());
            $form_element_name = self :: FORM_ELEMENT_METADATA_PREFIX . '$' . $element->render_name() . '[' .
                 self :: FORM_ELEMENT_VALUE . ']';
            
            $default_values[$form_element_name] = $element_value->get_element_vocabulary_id() ? $element_value->get_element_vocabulary_id() : $element_value->get_value();
            
            foreach ($element_value->get_attribute_values() as $attribute_value)
            {
                $attribute = DataManager :: retrieve_by_id(
                    Attribute :: class_name(), 
                    $attribute_value->get_attribute_id());
                $form_element_name = self :: FORM_ELEMENT_METADATA_PREFIX . '$' . $element->render_name() . '[' .
                     $attribute->render_name() . ']';
                
                $default_values[$form_element_name] = $attribute_value->get_attribute_vocabulary_id() ? $attribute_value->get_attribute_vocabulary_id() : $attribute_value->get_value();
            }
        }
        
        $this->form->setDefaults($default_values);
    }
}