<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Type\Form;

use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Type\Storage\DataClass\ContentObjectRelMetadataElement;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Builds the form for the content_object_rel_metadata_element
 * 
 * @package repository\integration\core\metadata\linker\type
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectRelMetadataElementFormBuilder
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
            $form = new FormValidator('content_object_rel_metadata_element');
        }
        
        $this->form = $form;
    }

    /**
     * Builds the form
     * 
     * @param ContentObjectRelMetadataElement $content_object_rel_metadata_element
     */
    public function build_form(ContentObjectRelMetadataElement $content_object_rel_metadata_element = null)
    {
        $form = $this->form;
        
        $content_object_types = \Chamilo\Core\Repository\Storage\DataManager :: get_registered_types();
        $types = array();
        
        $types[''] = Translation :: get('AllContentObjects');
        
        foreach ($content_object_types as $content_object_type)
        {
            $content_object_type = ClassnameUtilities :: getInstance()->getNamespaceFromClassname($content_object_type);
            $types[$content_object_type] = Translation :: get('TypeName', null, $content_object_type);
        }
        
        asort($types);
        
        $form->addElement(
            'select', 
            ContentObjectRelMetadataElement :: PROPERTY_CONTENT_OBJECT_TYPE, 
            Translation :: get('ContentObjectType'), 
            $types, 
            array('class' => 'type_selector'));
        
        $metadata_elements = \Chamilo\Core\Metadata\Storage\DataManager :: retrieve_element_names_with_schema_namespaces();
        
        $form->addElement(
            'select', 
            ContentObjectRelMetadataElement :: PROPERTY_METADATA_ELEMENT_ID, 
            Translation :: get('MetadataElement'), 
            $metadata_elements);
        
        $form->addElement(
            'checkbox', 
            ContentObjectRelMetadataElement :: PROPERTY_REQUIRED, 
            Translation :: get('Required'));
        
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
        
        if ($content_object_rel_metadata_element && $content_object_rel_metadata_element->is_identified())
        {
            $this->set_default_values($content_object_rel_metadata_element);
        }
    }

    /**
     * Sets the default values
     * 
     * @param ContentObjectRelMetadataElement $content_object_rel_metadata_element
     */
    protected function set_default_values(ContentObjectRelMetadataElement $content_object_rel_metadata_element)
    {
        $default_values = array();
        
        $default_values[ContentObjectRelMetadataElement :: PROPERTY_CONTENT_OBJECT_TYPE] = $content_object_rel_metadata_element->get_content_object_type();
        
        $default_values[ContentObjectRelMetadataElement :: PROPERTY_METADATA_ELEMENT_ID] = $content_object_rel_metadata_element->get_metadata_element_id();
        
        $default_values[ContentObjectRelMetadataElement :: PROPERTY_REQUIRED] = $content_object_rel_metadata_element->is_required();
        
        $this->form->setDefaults($default_values);
    }
}