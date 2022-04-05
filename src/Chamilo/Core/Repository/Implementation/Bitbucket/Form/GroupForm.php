<?php
namespace Chamilo\Core\Repository\Implementation\Bitbucket\Form;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class GroupForm extends FormValidator
{

    /**
     * The renderer used to display the form
     */
    private $renderer;

    private $bitbucket;
    const PERMISSION = 'permission';
    const TYPE_READ = 'read';
    const TYPE_WRITE = 'write';
    const TYPE_ADMIN = 'admin';

    public function __construct($action, $bitbucket)
    {
        parent::__construct(ClassnameUtilities::getInstance()->getClassnameFromObject($this, true), 'post', $action);
        $this->renderer = clone $this->defaultRenderer();
        
        $this->bitbucket = $bitbucket;
        $this->build();
        
        $this->accept($this->renderer);
    }

    public function build()
    {
        $this->renderer->setElementTemplate(
            '<div style="vertical-align: middle; float: left; margin-right: 5px;">{element}</div>');
        $this->addElement('text', 'group_name', Translation::get('GroupName'));
        $this->addElement('select', self::PERMISSION, Translation::get('Permission'), $this->get_privileges_types());
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

    /**
     * Display the form
     */
    public function toHtml($in_data = null)
    {
        $html = array();
        $html[] = '<div>';
        $html[] = $this->renderer->toHTML();
        $html[] = '</div>';
        return implode('', $html);
    }

    public function create_group()
    {
        $values = $this->exportValues();
        return $this->bitbucket->get_external_repository_manager_connector()->create_group(
            $values['group_name'], 
            $values['permission']);
    }

    public static function get_privileges_types()
    {
        $privileges_types = array();
        $privileges_types[self::TYPE_READ] = Translation::get('Read');
        $privileges_types[self::TYPE_WRITE] = Translation::get('Write');
        $privileges_types[self::TYPE_ADMIN] = Translation::get('Admin');
        
        return $privileges_types;
    }
}
