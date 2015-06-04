<?php
namespace Chamilo\Core\MetadataOld\Element\Form;

use Chamilo\Core\MetadataOld\Attribute\Entity\AttributeEntity;
use Chamilo\Core\MetadataOld\Element\Ajax\Component\ElementEntityFeedComponent;
use Chamilo\Core\MetadataOld\Element\Entity\ElementEntity;
use Chamilo\Core\MetadataOld\Element\Manager;
use Chamilo\Core\MetadataOld\Element\Storage\DataClass\Element;
use Chamilo\Core\MetadataOld\Element\Storage\DataClass\ElementNesting;
use Chamilo\Core\MetadataOld\Element\Storage\DataClass\ElementRelAttribute;
use Chamilo\Core\MetadataOld\Element\Storage\DataManager;
use Chamilo\Core\MetadataOld\Schema\Storage\DataClass\Schema;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Form for the element
 */
class ElementForm extends FormValidator
{

    /**
     * Constructor
     *
     * @param string $form_url
     * @param Element $element
     */
    public function __construct($form_url, $element = null)
    {
        parent :: __construct('element', 'post', $form_url);

        $this->build_form($element);

        if ($element && $element->is_identified())
        {
            $this->set_defaults($element);
        }
    }

    /**
     * Builds this form
     */
    protected function build_form(Element $element)
    {
        $parameters = new DataClassRetrievesParameters();
        $parameters->set_order_by(
            array(new OrderBy(new PropertyConditionVariable(Schema :: class_name(), Schema :: PROPERTY_NAMESPACE))));
        $schemas = \Chamilo\Core\MetadataOld\Storage\DataManager :: retrieves(Schema :: class_name());

        while ($schema = $schemas->next_result())
        {
            $options[$schema->get_id()] = $schema->get_namespace() . ' - ' . $schema->get_name();
        }

        $this->addElement(
            'select',
            Element :: PROPERTY_SCHEMA_ID,
            Translation :: get('Prefix', null, 'Chamilo\Core\MetadataOld'),
            $options);

        $this->addRule(
            Element :: PROPERTY_SCHEMA_ID,
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
            'required');

        $this->addElement(
            'text',
            Element :: PROPERTY_NAME,
            Translation :: get('Name', null, Utilities :: COMMON_LIBRARIES));
        $this->addRule(
            Element :: PROPERTY_NAME,
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
            'required');

        $this->addElement(
            'text',
            Element :: PROPERTY_DISPLAY_NAME,
            Translation :: get('DisplayName', null, 'Chamilo\Core\MetadataOld'));
        $this->addRule(
            Element :: PROPERTY_DISPLAY_NAME,
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
            'required');

        $types = new AdvancedElementFinderElementTypes();

        $types->add_element_type(
            new AdvancedElementFinderElementType(
                'elements',
                Translation :: get('Elements'),
                Manager :: context(),
                'element_entity_feed',
                $element ? array(ElementEntityFeedComponent :: PARAM_ELEMENT_ID => $element->get_id()) : array()));

        $types->add_element_type(AttributeEntity :: get_element_finder_type());
        $this->addElement(
            'advanced_element_finder',
            Manager :: PROPERTY_ASSOCIATIONS,
            Translation :: get('Associations'),
            $types);

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation :: get('Save', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'positive'));

        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Sets the default values
     *
     * @param Element $element
     */
    protected function set_defaults($element)
    {
        $defaults = array();

        $defaults[Element :: PROPERTY_SCHEMA_ID] = $element->get_schema_id();
        $defaults[Element :: PROPERTY_NAME] = $element->get_name();
        $defaults[Element :: PROPERTY_DISPLAY_NAME] = $element->get_display_name();

        // Get the default associations
        $default_elements = new AdvancedElementFinderElements();

        // Element nesting
        $element_entity = ElementEntity :: get_instance();

        $condition = new EqualityCondition(
            new PropertyConditionVariable(ElementNesting :: class_name(), ElementNesting :: PROPERTY_PARENT_ELEMENT_ID),
            new StaticConditionVariable($element->get_id()));

        $element_nestings = DataManager :: retrieves(
            ElementNesting :: class_name(),
            new DataClassRetrievesParameters($condition));

        while ($element_nesting = $element_nestings->next_result())
        {
            $default_elements->add_element(
                $element_entity->get_element_finder_element($element_nesting->get_child_element_id()));
        }

        // Element-Attribute relations
        $attribute_entity = AttributeEntity :: get_instance();

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ElementRelAttribute :: class_name(),
                ElementRelAttribute :: PROPERTY_ELEMENT_ID),
            new StaticConditionVariable($element->get_id()));

        $element_rel_attributes = DataManager :: retrieves(
            ElementRelAttribute :: class_name(),
            new DataClassRetrievesParameters($condition));

        while ($element_rel_attribute = $element_rel_attributes->next_result())
        {
            $default_elements->add_element(
                $attribute_entity->get_element_finder_element($element_rel_attribute->get_attribute_id()));
        }

        $this->getElement(Manager :: PROPERTY_ASSOCIATIONS)->setDefaultValues($default_elements);

        $this->setDefaults($defaults);
    }
}