<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Alternative\Form;

use Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Alternative\Storage\DataClass\ContentObjectAlternative;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Builds the form for the content_object_alternative
 * 
 * @package repository\content_object_property_metadata_linker
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectAlternativeFormBuilder
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
            $form = new FormValidator('content_object_alternative');
        }
        
        $this->form = $form;
    }

    /**
     * Builds the form
     * 
     * @param ContentObjectAlternative $content_object_alternative
     * @param array $allowed_metdata_elements
     */
    public function build_form(ContentObjectAlternative $content_object_alternative = null, 
        $allowed_metdata_elements = array())
    {
        $form = $this->form;
        
        $metadata_elements = array();
        
        foreach ($allowed_metdata_elements as $metadata_element)
        {
            $metadata_elements[$metadata_element->get_id()] = $metadata_element->get_display_name();
        }
        
        if (count($metadata_elements) == 0)
        {
            $form->addElement(
                'html', 
                '<div class="warning-message">' . Translation :: get('NoValidMetadataElementsAvailable') . '</div>');
        }
        else
        {
            $form->addElement(
                'select', 
                ContentObjectAlternative :: PROPERTY_METADATA_ELEMENT_ID, 
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
        }
        
        if ($content_object_alternative && $content_object_alternative->is_identified())
        {
            $this->set_default_values($content_object_alternative);
        }
    }

    /**
     * Sets the default values
     * 
     * @param ContentObjectAlternative $content_object_alternative
     */
    protected function set_default_values(ContentObjectAlternative $content_object_alternative)
    {
        $default_values = array();
        
        $default_values[ContentObjectAlternative :: PROPERTY_METADATA_ELEMENT_ID] = $content_object_alternative->get_metadata_element_id();
        
        $this->form->setDefaults($default_values);
    }
}