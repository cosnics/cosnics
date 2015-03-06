<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Property\Form;

use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Property\Storage\DataClass\ContentObjectPropertyRelMetadataElement;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Property\Storage\DataManager;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Builds the form for the content_object_property_rel_metadata_element
 *
 * @package repository\content_object_property_metadata_linker
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectPropertyRelMetadataElementFormBuilder
{

    /**
     * The FormValidator
     *
     * @var \libraries\format\FormValidator
     */
    private $form;

    /**
     * Constructor
     *
     * @param \libraries\format\FormValidator $form
     */
    public function __construct(FormValidator $form)
    {
        if (! $form)
        {
            $form = new FormValidator('content_object_property_rel_metadata_element');
        }

        $this->form = $form;
    }

    /**
     * Builds the form
     *
     * @param ContentObjectPropertyRelMetadataElement $content_object_property_rel_metadata_element
     */
    public function build_form(
        ContentObjectPropertyRelMetadataElement $content_object_property_rel_metadata_element = null)
    {
        $form = $this->form;

        $implementation_packages = DataManager :: get_implementation_packages();
        array_unshift($implementation_packages, __NAMESPACE__);

        $types = array();
        $property_names = array();

        foreach ($implementation_packages as $implementation_package)
        {
            $types[$implementation_package] = Translation :: get('ContentObjectTypeName', null, $implementation_package);

            $property_provider = DataManager :: get_property_provider_from_implementation($implementation_package);
            $properties = $property_provider->get_properties();

            foreach ($properties as $property)
            {
                $property_names[$implementation_package][$property] = Translation :: get(
                    (string) StringUtilities :: getInstance()->createString($property)->upperCamelize(),
                    null,
                    $implementation_package);
            }
        }

        $javascript = array();

        $javascript[] = '<script type="text/javascript">';
        $javascript[] = 'property_names = ' . json_encode($property_names) . ';';
        $javascript[] = '</script>';

        $javascript[] = ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->namespaceToFullPath(
                'Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Property',
                true) . 'ContentObjectPropertyRelMetadataElementForm.js');

        $form->addElement('html', implode(PHP_EOL, $javascript));

        $form->addElement(
            'select',
            ContentObjectPropertyRelMetadataElement :: PROPERTY_CONTENT_OBJECT_TYPE,
            Translation :: get('ContentObjectType'),
            $types,
            array('class' => 'type_selector'));

        $current_type = $this->form->exportValue(
            ContentObjectPropertyRelMetadataElement :: PROPERTY_CONTENT_OBJECT_TYPE);

        if (! isset($current_type))
        {
            if ($content_object_property_rel_metadata_element &&
                 $content_object_property_rel_metadata_element->is_identified())
            {
                $current_type = $content_object_property_rel_metadata_element->get_content_object_type();
            }
            else
            {
                $current_type = $implementation_packages[0];
            }
        }

        $form->addElement(
            'select',
            ContentObjectPropertyRelMetadataElement :: PROPERTY_PROPERTY_NAME,
            Translation :: get('PropertyName'),
            $property_names[$current_type],
            array('class' => 'property_name_selector'));

        $metadata_elements = \Chamilo\Core\Metadata\Storage\DataManager :: retrieve_element_names_with_schema_namespaces();

        $form->addElement(
            'select',
            ContentObjectPropertyRelMetadataElement :: PROPERTY_METADATA_ELEMENT_ID,
            Translation :: get('MetadataElement'),
            $metadata_elements);

        $buttons = array();

        $buttons[] = $form->createElement(
            'style_submit_button',
            'submit',
            Translation :: get('Save', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'positive'));

        $buttons[] = $form->createElement(
            'style_reset_button',
            'reset',
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'normal empty'));

        $form->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        if ($content_object_property_rel_metadata_element &&
             $content_object_property_rel_metadata_element->is_identified())
        {
            $this->set_default_values($content_object_property_rel_metadata_element);
        }
    }

    /**
     * Sets the default values
     *
     * @param ContentObjectPropertyRelMetadataElement $content_object_property_rel_metadata_element
     */
    protected function set_default_values(
        ContentObjectPropertyRelMetadataElement $content_object_property_rel_metadata_element)
    {
        $default_values = array();

        $default_values[ContentObjectPropertyRelMetadataElement :: PROPERTY_CONTENT_OBJECT_TYPE] = $content_object_property_rel_metadata_element->get_content_object_type();

        $default_values[ContentObjectPropertyRelMetadataElement :: PROPERTY_PROPERTY_NAME] = $content_object_property_rel_metadata_element->get_property_name();

        $default_values[ContentObjectPropertyRelMetadataElement :: PROPERTY_METADATA_ELEMENT_ID] = $content_object_property_rel_metadata_element->get_metadata_element_id();

        $this->form->setDefaults($default_values);
    }
}