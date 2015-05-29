<?php
namespace Chamilo\Core\MetadataOld\Attribute\Form;

use Chamilo\Core\MetadataOld\Attribute\Storage\DataClass\Attribute;
use Chamilo\Core\MetadataOld\Schema\Storage\DataClass\Schema;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Form for the element
 */
class AttributeForm extends FormValidator
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

        $this->build_form();

        if ($element && $element->is_identified())
        {
            $this->set_defaults($element);
        }
    }

    /**
     * Builds this form
     */
    protected function build_form()
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
            Attribute :: PROPERTY_SCHEMA_ID,
            Translation :: get('Prefix', null, 'Chamilo\Core\Metadata'),
            $options);

        $this->addRule(
            Attribute :: PROPERTY_SCHEMA_ID,
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
            'required');

        $this->addElement(
            'text',
            Attribute :: PROPERTY_NAME,
            Translation :: get('Name', null, Utilities :: COMMON_LIBRARIES));
        $this->addRule(
            Attribute :: PROPERTY_NAME,
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
            'required');

        $this->addElement(
            'text',
            Attribute :: PROPERTY_DISPLAY_NAME,
            Translation :: get('DisplayName', null, 'Chamilo\Core\Metadata'));
        $this->addRule(
            Attribute :: PROPERTY_DISPLAY_NAME,
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
            'required');

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
     * @param Attribute $attribute
     */
    protected function set_defaults($attribute)
    {
        $defaults = array();

        $defaults[Attribute :: PROPERTY_SCHEMA_ID] = $attribute->get_schema_id();
        $defaults[Attribute :: PROPERTY_NAME] = $attribute->get_name();
        $defaults[Attribute :: PROPERTY_DISPLAY_NAME] = $attribute->get_display_name();

        $this->setDefaults($defaults);
    }
}