<?php
namespace Chamilo\Core\Metadata\Schema\Form;

use Chamilo\Core\Metadata\Schema\Storage\DataClass\Schema;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Form for the schema
 */
class SchemaForm extends FormValidator
{

    /**
     * Constructor
     * 
     * @param string $form_url
     * @param Schema $schema
     */
    public function __construct($form_url, $schema = null)
    {
        parent :: __construct('schema', 'post', $form_url);
        
        $this->build_form();
        
        if ($schema && $schema->is_identified())
        {
            $this->set_defaults($schema);
        }
    }

    /**
     * Builds this form
     */
    protected function build_form()
    {
        $this->addElement('text', Schema :: PROPERTY_NAMESPACE, Translation :: get('Namespace'), array("size" => "50"));
        
        $this->addRule(
            Schema :: PROPERTY_NAMESPACE, 
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 
            'required');
        
        $this->addElement('text', Schema :: PROPERTY_NAME, Translation :: get('Name'), array("size" => "50"));
        
        $this->addRule(
            Schema :: PROPERTY_NAME, 
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 
            'required');
        
        $this->addElement('text', Schema :: PROPERTY_URL, Translation :: get('Url'), array("size" => "50"));
        
        $this->addRule(
            Schema :: PROPERTY_URL, 
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
     * @param Schema $schema
     */
    protected function set_defaults($schema)
    {
        $defaults = array();
        
        $defaults[Schema :: PROPERTY_NAMESPACE] = $schema->get_namespace();
        $defaults[Schema :: PROPERTY_NAME] = $schema->get_name();
        $defaults[Schema :: PROPERTY_URL] = $schema->get_url();
        
        $this->setDefaults($defaults);
    }
}