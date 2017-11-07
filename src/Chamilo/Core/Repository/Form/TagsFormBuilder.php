<?php
namespace Chamilo\Core\Repository\Form;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;

/**
 * Builds the tags form in a given formvalidator
 * 
 * @package repository
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TagsFormBuilder
{
    const PROPERTY_TAGS = 'tags';

    /**
     * The FormValidator
     * 
     * @var \libraries\format\FormValidator
     */
    private $form;

    /**
     * Constructor
     * 
     * @param FormValidator $form
     */
    public function __construct(FormValidator $form)
    {
        $this->form = $form;
    }

    /**
     * Builds the form
     * 
     * @param array $available_tags
     * @param array $default_tags
     */
    public function build_form($available_tags, $default_tags = array())
    {
        $this->add_dependencies();
        
        $this->form->add_textfield(
            self::PROPERTY_TAGS, 
            Translation::get('Tags'), 
            false, 
            array('id' => 'tagsinput', 'style' => 'width: 95%'));
        
        $this->add_javascript($available_tags);
        $this->set_defaults($default_tags);
    }

    /**
     * Adds the dependencies to the form
     */
    protected function add_dependencies()
    {
        $resource_manager = ResourceManager::getInstance();
        $plugin_path = Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository', true) .
             'Plugin/Bootstrap/Tagsinput/';
        
        $dependencies = array();
        
        $dependencies[] = $resource_manager->get_resource_html($plugin_path . 'bootstrap-typeahead.js');
        
        $dependencies[] = $resource_manager->get_resource_html($plugin_path . 'bootstrap-tagsinput.min.js');
        
        $dependencies[] = $resource_manager->get_resource_html($plugin_path . 'bootstrap-tagsinput.css');
        
        $this->form->addElement('html', implode(PHP_EOL, $dependencies));
    }

    /**
     * Adds the javascript to the form
     * 
     * @param array $available_tags
     */
    protected function add_javascript($available_tags = array())
    {
        $json = json_encode($available_tags);
        
        $html = array();
        
        $html[] = '<script type="text/javascript">';
        $html[] = '$(\'#tagsinput\').tagsinput({';
        $html[] = 'typeahead: {';
        $html[] = 'name: \'tags\',';
        $html[] = 'source: ' . $json . ',';
        $html[] = 'local: ' . $json;
        $html[] = '}';
        $html[] = '});';
        $html[] = '</script>';
        
        $this->form->addElement('html', implode(PHP_EOL, $html));
    }

    /**
     * Set the default values for this form
     * 
     * @param array $default_tags
     */
    protected function set_defaults($default_tags = array())
    {
        $defaults = array();
        
        $defaults[self::PROPERTY_TAGS] = implode(',', $default_tags);
        
        $this->form->setDefaults($defaults);
    }
}