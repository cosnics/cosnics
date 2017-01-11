<?php
namespace Chamilo\Core\Admin\Announcement\Form;

use Chamilo\Core\Admin\Announcement\Manager;
use Chamilo\Core\Admin\Announcement\Rights;
use Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class PublicationForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_UPDATE = 2;
    const PROPERTY_TARGETS = 'targets';
    const PROPERTY_FOREVER = 'forever';
    const PROPERTY_FROM_DATE = 'from_date';
    const PROPERTY_TO_DATE = 'to_date';
    const PROPERTY_PUBLISH_AND_BUILD = 'publish_and_build';
    
    // Rights
    const PROPERTY_INHERIT = 'inherit';
    const PROPERTY_RIGHT_OPTION = 'right_option';
    const PROPERTY_COLLABORATE = 'collaborate';
    const INHERIT_TRUE = 0;
    const INHERIT_FALSE = 1;
    const RIGHT_OPTION_ALL = 0;
    const RIGHT_OPTION_ME = 1;
    const RIGHT_OPTION_SELECT = 2;

    /**
     * The type of the form (create or edit)
     * 
     * @var int
     */
    private $form_type;

    /**
     * The publications that will be created / edited
     */
    private $publications;

    /**
     * Available entities for the view rights
     * 
     * @var Array
     */
    private $entities;

    public function __construct($form_type, $publications, $action)
    {
        parent::__construct('publish', 'post', $action);
        
        if (count($publications) <= 0)
        {
            throw new NoObjectSelectedException(Translation::get('Publication'));
        }
        
        $this->publications = $publications;
        $this->form_type = $form_type;
        
        $this->entities = array();
        $this->entities[UserEntity::ENTITY_TYPE] = new UserEntity();
        $this->entities[PlatformGroupEntity::ENTITY_TYPE] = new PlatformGroupEntity();
        
        switch ($form_type)
        {
            case self::TYPE_CREATE :
                $this->build_create_form();
                break;
            case self::TYPE_UPDATE :
                $this->build_update_form();
                break;
        }
        
        $this->setDefaults();
    }

    /**
     * Builds the create form with the publish button
     */
    public function build_create_form()
    {
        $this->build_form();
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            self::PARAM_SUBMIT, 
            Translation::get('Publish', null, Utilities::COMMON_LIBRARIES), 
            null, 
            null, 
            'arrow-right');
        
        $buttons[] = $this->createElement(
            'style_reset_button', 
            self::PARAM_RESET, 
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Builds the update form with the update button
     */
    public function build_update_form()
    {
        $this->build_form();
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            self::PARAM_SUBMIT, 
            Translation::get('Update', null, Utilities::COMMON_LIBRARIES), 
            null, 
            null, 
            'arrow-right');
        $buttons[] = $this->createElement(
            'style_reset_button', 
            self::PARAM_RESET, 
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Builds the form by adding the necessary form elements.
     */
    public function build_form()
    {
        $this->build_rights_form();
        
        $this->add_forever_or_timewindow();
        $this->addElement(
            'checkbox', 
            Publication::PROPERTY_HIDDEN, 
            Translation::get('Hidden', null, Utilities::COMMON_LIBRARIES));
    }

    /**
     * Builds the form to set the view rights
     */
    public function build_rights_form()
    {
        // Add the rights options
        $group = array();
        
        $group[] = & $this->createElement(
            'radio', 
            null, 
            null, 
            Translation::get('Everyone'), 
            self::RIGHT_OPTION_ALL, 
            array('class' => 'other_option_selected'));
        $group[] = & $this->createElement(
            'radio', 
            null, 
            null, 
            Translation::get('OnlyForMe'), 
            self::RIGHT_OPTION_ME, 
            array('class' => 'other_option_selected'));
        $group[] = & $this->createElement(
            'radio', 
            null, 
            null, 
            Translation::get('SelectSpecificEntities'), 
            self::RIGHT_OPTION_SELECT, 
            array('class' => 'entity_option_selected'));
        
        $this->addGroup(
            $group, 
            self::PROPERTY_RIGHT_OPTION, 
            Translation::get('PublishFor', null, Utilities::COMMON_LIBRARIES), 
            '');
        
        // Add the advanced element finder
        $types = new AdvancedElementFinderElementTypes();
        
        foreach ($this->entities as $entity)
        {
            $types->add_element_type($entity->get_element_finder_type());
        }
        
        $this->addElement('html', '<div style="margin-left:25px; display:none;" class="entity_selector_box">');
        $this->addElement('advanced_element_finder', self::PROPERTY_TARGETS, null, $types);
        
        $this->addElement('html', '</div>');
        
        $this->addElement(
            'html', 
            ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->getJavascriptPath('Chamilo\Core\Admin\Announcement', true) . 'RightsForm.js'));
    }

    /**
     * Sets the default values of the form.
     * By default the publication is for everybody who has access to the tool and
     * the publication will be available forever.
     */
    public function setDefaults()
    {
        $defaults = array();
        
        $publications = $this->publications;
        
        $defaults[self::PROPERTY_FOREVER] = 1;
        $defaults[self::PROPERTY_RIGHT_OPTION] = self::RIGHT_OPTION_ALL;
        
        if (count($publications) == 1)
        {
            $first_publication = $publications[0];
            
            if ($first_publication->get_id())
            {
                if ($first_publication->get_from_date() != 0)
                {
                    $defaults[self::PROPERTY_FOREVER] = 0;
                    $defaults[Publication::PROPERTY_FROM_DATE] = $first_publication->get_from_date();
                    $defaults[Publication::PROPERTY_TO_DATE] = $first_publication->get_to_date();
                }
                
                $defaults[Publication::PROPERTY_HIDDEN] = $first_publication->is_hidden();
            }
            
            $defaults[self::PROPERTY_RIGHT_OPTION] = self::RIGHT_OPTION_ALL;
        }
        
        parent::setDefaults($defaults);
    }

    /**
     * Handles the submit of the form for both create and edit
     * 
     * @return boolean
     */
    public function handle_form_submit()
    {
        $publications = $this->publications;
        $succes = true;
        
        foreach ($publications as $publication)
        {
            $this->set_publication_values($publication);
            
            switch ($this->form_type)
            {
                case self::TYPE_CREATE :
                    $succes &= $publication->create();
                    $this->set_publication_rights($publication);
                    break;
                case self::TYPE_UPDATE :
                    $succes &= $publication->update();
                    $this->set_publication_rights($publication);
                    break;
            }
        }
        return $succes;
    }

    /**
     * Sets the values for the content object publication
     * 
     * @param $publication ContentObjectPublication
     */
    public function set_publication_values(Publication $publication)
    {
        $values = $this->exportValues();
        
        if ($values[self::PROPERTY_FOREVER] != 0)
        {
            $from = $to = 0;
        }
        else
        {
            $from = DatetimeUtilities::time_from_datepicker($values[self::PROPERTY_FROM_DATE]);
            $to = DatetimeUtilities::time_from_datepicker($values[self::PROPERTY_TO_DATE]);
        }
        
        $publication->set_from_date($from);
        $publication->set_to_date($to);
        $publication->set_publication_date(time());
        $publication->set_modification_date(time());
        $publication->set_hidden($values[Publication::PROPERTY_HIDDEN] ? 1 : 0);
    }

    /**
     * Sets the rights for the given content object publication
     * 
     * @param $publication Publication
     * @param $category_changed boolean
     */
    public function set_publication_rights(Publication $publication)
    {
        $values = $this->exportValues();
        
        $location = Rights::getInstance()->get_location_by_identifier(
            Manager::context(), 
            Rights::TYPE_PUBLICATION, 
            $publication->get_id());
        
        if (! $location->clear_right(Rights::VIEW_RIGHT))
        {
            return false;
        }
        
        if ($location->inherits())
        {
            $location->disinherit();
            if (! $location->update())
            {
                return false;
            }
        }
        
        $option = $values[self::PROPERTY_RIGHT_OPTION];
        $location_id = $location->get_id();
        
        $rights = Rights::getInstance();
        
        switch ($option)
        {
            case self::RIGHT_OPTION_ALL :
                if (! $rights->invert_location_entity_right(Manager::context(), Rights::VIEW_RIGHT, 0, 0, $location_id))
                {
                    return false;
                }
                break;
            case self::RIGHT_OPTION_ME :
                if (! $rights->invert_location_entity_right(
                    Manager::context(), 
                    Rights::VIEW_RIGHT, 
                    Session::get_user_id(), 
                    UserEntity::ENTITY_TYPE, 
                    $location_id))
                {
                    return false;
                }
                break;
            case self::RIGHT_OPTION_SELECT :
                foreach ($values[self::PROPERTY_TARGETS] as $entity_type => $target_ids)
                {
                    foreach ($target_ids as $target_id)
                    {
                        if (! $rights->invert_location_entity_right(
                            Manager::context(), 
                            Rights::VIEW_RIGHT, 
                            $target_id, 
                            $entity_type, 
                            $location_id))
                        {
                            return false;
                        }
                    }
                }
        }
    }
}
