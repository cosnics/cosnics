<?php
namespace Chamilo\Application\Survey\Rights\Form;

use Chamilo\Application\Survey\Service\RightsService;
use Chamilo\Application\Survey\Storage\DataClass\PublicationEntityRelation;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Application\Survey\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RightsForm extends FormValidator
{
    const PROPERTY_ACCESS = 'targets';
    const PROPERTY_TAKE = 'right_take';
    const PROPERTY_MAIL = 'right_mail';
    const PROPERTY_REPORT = 'right_report';
    const PROPERTY_MANAGE = 'right_manage';
    const PROPERTY_PUBLISH = 'right_publish';

    /**
     *
     * @var \Chamilo\Application\Survey\Storage\DataClass\PublicationEntityRelation
     */
    private $entityRelation;

    /**
     *
     * @var int
     */
    private $rightType;

    /**
     *
     * @param string $formUrl
     */
    public function __construct($formUrl, PublicationEntityRelation $entityRelation = null, $rightType)
    {
        parent :: __construct('rights', 'post', $formUrl);
        
        $this->entityRelation = $entityRelation;
        $this->rightType = $rightType;
        $this->build_form();
        $this->setDefaults();
    }

    public function build_form()
    {
        if ($this->entityRelation instanceof PublicationEntityRelation)
        {
            if ($this->entityRelation->getEntityType() == UserEntity :: ENTITY_TYPE)
            {
                $entityType = UserEntity :: getInstance()->get_entity_translated_name();
                $entityName = \Chamilo\Libraries\Storage\DataManager\DataManager :: retrieve_by_id(
                    User :: class_name(), 
                    $this->entityRelation->getEntityId())->get_fullname();
            }
            else
            {
                $entityType = PlatformGroupEntity :: getInstance()->get_entity_translated_name();
                $entityName = \Chamilo\Libraries\Storage\DataManager\DataManager :: retrieve_by_id(
                    Group :: class_name(), 
                    $this->entityRelation->getEntityId())->get_name();
            }
            
            $this->addElement('static', null, $entityType, $entityName);
        }
        else
        {
            $types = new AdvancedElementFinderElementTypes();
            $types->add_element_type(UserEntity :: get_element_finder_type());
            $types->add_element_type(PlatformGroupEntity :: get_element_finder_type());
            $this->addElement(
                'advanced_element_finder', 
                self :: PROPERTY_ACCESS, 
                Translation :: get('UsersGroups'), 
                $types);
        }
        
        if ($this->rightType == RightsService :: PUBLICATION_RIGHTS)
        {
            $this->addElement(
                'checkbox', 
                self :: PROPERTY_TAKE, 
                Translation :: get('TakeRight'), 
                null, 
                null, 
                RightsService :: RIGHT_VIEW | RightsService :: RIGHT_TAKE);
            
            $this->addElement(
                'checkbox', 
                self :: PROPERTY_MAIL, 
                Translation :: get('MailRight'), 
                null, 
                null, 
                RightsService :: RIGHT_VIEW | RightsService :: RIGHT_MAIL);
            
            $this->addElement(
                'checkbox', 
                self :: PROPERTY_REPORT, 
                Translation :: get('ReportRight'), 
                null, 
                null, 
                RightsService :: RIGHT_VIEW | RightsService :: RIGHT_REPORT);
            
            $this->add_warning_message(null, null, Translation :: get('ManageRightWarning'), true);
            
            $this->addElement(
                'checkbox', 
                self :: PROPERTY_MANAGE, 
                Translation :: get('ManageRight'), 
                null, 
                null, 
                RightsService :: RIGHT_VIEW | RightsService :: RIGHT_MAIL | RightsService :: RIGHT_REPORT |
                     RightsService :: RIGHT_MANAGE);
        }elseif($this->rightType == RightsService::APPLICATION_RIGHTS){
            
            $this->addElement(
                'checkbox',
                self :: PROPERTY_PUBLISH,
                Translation :: get('PublishRight'),
                null,
                null,
                RightsService :: RIGHT_PUBLISH);
            
        }
        
        $this->addElement(
            'html', 
            ResourceManager :: getInstance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath('Chamilo\Application\Survey\Rights', true) . 'RightsForm.js'));
        
        $this->addSaveResetButtons();
    }

    public function setDefaults($defaults = array())
    {
        if ($this->entityRelation instanceof PublicationEntityRelation)
        {
            $givenRights = $this->entityRelation->getRights();
            
            $defaults[self :: PROPERTY_TAKE] = $givenRights & RightsService :: RIGHT_TAKE;
            $defaults[self :: PROPERTY_MAIL] = $givenRights & RightsService :: RIGHT_MAIL;
            $defaults[self :: PROPERTY_REPORT] = $givenRights & RightsService :: RIGHT_REPORT;
            $defaults[self :: PROPERTY_MANAGE] = $givenRights & RightsService :: RIGHT_MANAGE;
            $defaults[self :: PROPERTY_PUBLISH] = $givenRights & RightsService :: RIGHT_PUBLISH;
        }
        else
        {
            // $defaults[self :: PROPERTY_VIEW] = RightsService :: RIGHT_VIEW;
        }
        
        parent :: setDefaults($defaults);
    }
}
