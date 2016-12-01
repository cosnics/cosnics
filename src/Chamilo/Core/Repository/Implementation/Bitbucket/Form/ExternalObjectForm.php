<?php
namespace Chamilo\Core\Repository\Implementation\Bitbucket\Form;

use Chamilo\Core\Repository\Implementation\Bitbucket\ExternalObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class ExternalObjectForm extends FormValidator
{

    /**
     * The renderer used to display the form
     */
    private $bitbucket;

    public function __construct($action, $bitbucket)
    {
        parent::__construct(ClassnameUtilities::getInstance()->getClassnameFromObject($this, true), 'post', $action);
        
        $this->bitbucket = $bitbucket;
        $this->build();
    }

    public function build()
    {
        $this->addElement('hidden', 'id');
        $this->addElement('text', 'name', Translation::get('Name'));
        $this->addElement('textarea', 'description', Translation::get('Description'));
        $this->addElement('text', 'website', Translation::get('Website'));
        $this->addElement(
            'style_submit_button', 
            'submit', 
            Translation::get('Create', null, Utilities::COMMON_LIBRARIES), 
            null, 
            null, 
            'arrow-right');
        
        $this->addElement(
            'html', 
            ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository\Implementation\Bitbucket', true) .
                     'PrivilegeGrantingForm.js'));
    }

    public function create_repository()
    {
        $values = $this->exportValues();
        return $this->bitbucket->get_external_repository_manager_connector()->create_repository($values);
    }

    public function update_repository()
    {
        $values = $this->exportValues();
        return $this->bitbucket->get_external_repository_manager_connector()->update_repository($values);
    }

    public function set_external_repository_object(ExternalObject $external_repository_object)
    {
        $this->external_repository_object = $external_repository_object;
        
        $defaults[ExternalObject::PROPERTY_ID] = $external_repository_object->get_id();
        $defaults[ExternalObject::PROPERTY_TITLE] = $external_repository_object->get_title();
        $defaults[ExternalObject::PROPERTY_DESCRIPTION] = html_entity_decode(
            $external_repository_object->get_description());
        $defaults[ExternalObject::PROPERTY_WEBSITE] = $external_repository_object->get_website();
        
        parent::setDefaults($defaults);
    }
}
