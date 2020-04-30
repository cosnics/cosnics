<?php
namespace Chamilo\Core\Repository\Workspace\Rights\Form;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Repository\Workspace\Rights\Manager;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RightsForm extends FormValidator
{
    const PROPERTY_ACCESS = 'targets';
    const PROPERTY_COPY = 'right_copy';
    const PROPERTY_USE = 'right_use';
    const PROPERTY_VIEW = 'right_view';
    const PROPERTY_MANAGE = 'right_manage';
    const MODE_CREATE = 'create';
    const MODE_UPDATE = 'update';

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation
     */
    private $entityRelation;

    /**
     * The form mode
     * 
     * @var string
     */
    protected $mode;

    /**
     *
     * @param string $formUrl
     * @param WorkspaceEntityRelation $entityRelation
     * @param string $mode
     */
    public function __construct($formUrl, WorkspaceEntityRelation $entityRelation = null, $mode = self::MODE_CREATE)
    {
        parent::__construct('rights', self::FORM_METHOD_POST, $formUrl);
        
        $this->entityRelation = $entityRelation;
        $this->mode = $mode;
        
        $this->build_form();
        $this->setDefaults();
    }

    public function build_form()
    {
        if ($this->entityRelation instanceof WorkspaceEntityRelation)
        {
            if ($this->entityRelation->get_entity_type() == UserEntity::ENTITY_TYPE)
            {
                $entityType = UserEntity::getInstance()->get_entity_translated_name();
                $entityName = DataManager::retrieve_by_id(
                    User::class,
                    $this->entityRelation->get_entity_id())->get_fullname();
            }
            else
            {
                $entityType = PlatformGroupEntity::getInstance()->get_entity_translated_name();
                $entityName = DataManager::retrieve_by_id(
                    Group::class,
                    $this->entityRelation->get_entity_id())->get_name();
            }
            
            $this->addElement('static', null, $entityType, $entityName);
        }
        else
        {
            $types = new AdvancedElementFinderElementTypes();
            $types->add_element_type(UserEntity::get_element_finder_type());
            $types->add_element_type(PlatformGroupEntity::get_element_finder_type());
            $this->addElement('advanced_element_finder', self::PROPERTY_ACCESS, Translation::get('UsersGroups'), $types);
        }
        
        $this->addElement(
            'radio', 
            self::PROPERTY_VIEW, 
            Translation::get('ContentRight'), 
            Translation::get('ViewRight'), 
            RightsService::RIGHT_VIEW);
        
        $this->addElement(
            'radio', 
            self::PROPERTY_VIEW, 
            null, 
            Translation::get('AddRight'), 
            RightsService::RIGHT_VIEW | RightsService::RIGHT_ADD);
        
        $this->addElement(
            'radio', 
            self::PROPERTY_VIEW, 
            null, 
            Translation::get('EditRight'), 
            RightsService::RIGHT_VIEW | RightsService::RIGHT_ADD | RightsService::RIGHT_EDIT);
        
        $this->addElement(
            'radio', 
            self::PROPERTY_VIEW, 
            null, 
            Translation::get('DeleteRight'), 
            RightsService::RIGHT_VIEW | RightsService::RIGHT_ADD | RightsService::RIGHT_EDIT |
                 RightsService::RIGHT_DELETE);
        
        $this->addElement(
            'checkbox', 
            self::PROPERTY_USE, 
            Translation::get('UseRight'), 
            null, 
            null, 
            RightsService::RIGHT_USE);
        
        $this->addElement(
            'checkbox', 
            self::PROPERTY_COPY, 
            Translation::get('CopyRight'), 
            null, 
            null, 
            RightsService::RIGHT_COPY);
        
        $this->addElement(
            'checkbox', 
            self::PROPERTY_MANAGE, 
            Translation::get('ManageRight'), 
            null, 
            null, 
            RightsService::RIGHT_VIEW | RightsService::RIGHT_ADD | RightsService::RIGHT_EDIT |
                 RightsService::RIGHT_DELETE | RightsService::RIGHT_USE | RightsService::RIGHT_COPY |
                 RightsService::RIGHT_MANAGE);
        
        $this->add_warning_message(null, null, Translation::get('ManageRightWarning'), true);
        
        $this->addElement(
            'html', 
            ResourceManager::getInstance()->getResourceHtml(
                Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository\Workspace\Rights', true) .
                     'RightsForm.js'));
        
        $this->addSaveResetButtons();
    }

    public function addSaveResetButtons()
    {
        $buttons = array();
        
        if ($this->mode == self::MODE_CREATE)
        {
            $buttons[] = $this->createElement(
                'style_submit_button', 
                'submit', 
                Translation::get('SaveAndAddNew', null, Manager::context()), 
                array('class' => 'positive'));
        }
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation::get('Save', null, Utilities::COMMON_LIBRARIES), 
            array('class' => 'positive'));
        
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES), 
            array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function setDefaults($defaults = array())
    {
        if ($this->entityRelation instanceof WorkspaceEntityRelation)
        {
            $givenRights = $this->entityRelation->get_rights();
            
            $defaults[self::PROPERTY_VIEW] = $givenRights & ~ RightsService::RIGHT_USE & ~ RightsService::RIGHT_COPY &
                 ~ RightsService::RIGHT_MANAGE;
            
            $defaults[self::PROPERTY_USE] = $givenRights & RightsService::RIGHT_USE;
            $defaults[self::PROPERTY_COPY] = $givenRights & RightsService::RIGHT_COPY;
            $defaults[self::PROPERTY_MANAGE] = $givenRights & RightsService::RIGHT_MANAGE;
        }
        else
        {
            $defaults[self::PROPERTY_VIEW] = RightsService::RIGHT_VIEW;
        }
        
        parent::setDefaults($defaults);
    }
}
