<?php
namespace Chamilo\Core\Admin\Announcement\Form;

use Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * @package Chamilo\Core\Admin\Announcement\Form
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
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
    const PROPERTY_RIGHT_OPTION = 'right_option';
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
     * Available entities for the view rights
     *
     * @var \Chamilo\Core\Rights\Entity\RightsEntity[]
     */
    private $entities;

    /**
     * @param int $formType
     * @param string $action
     */
    public function __construct($formType, $action)
    {
        parent::__construct('publish', 'post', $action);

        $this->form_type = $formType;

        $this->entities = array();
        $this->entities[UserEntity::ENTITY_TYPE] = new UserEntity();
        $this->entities[PlatformGroupEntity::ENTITY_TYPE] = new PlatformGroupEntity();

        switch ($formType)
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
            'style_submit_button', self::PARAM_SUBMIT, Translation::get('Publish', null, Utilities::COMMON_LIBRARIES),
            null, null, 'arrow-right'
        );

        $buttons[] = $this->createElement(
            'style_reset_button', self::PARAM_RESET, Translation::get('Reset', null, Utilities::COMMON_LIBRARIES)
        );

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
            'checkbox', Publication::PROPERTY_HIDDEN, Translation::get('Hidden', null, Utilities::COMMON_LIBRARIES)
        );
    }

    /**
     * Builds the form to set the view rights
     */
    public function build_rights_form()
    {
        // Add the rights options
        $group = array();

        $group[] = &$this->createElement(
            'radio', null, null, Translation::get('Everyone'), self::RIGHT_OPTION_ALL,
            array('class' => 'other_option_selected')
        );
        $group[] = &$this->createElement(
            'radio', null, null, Translation::get('OnlyForMe'), self::RIGHT_OPTION_ME,
            array('class' => 'other_option_selected')
        );
        $group[] = &$this->createElement(
            'radio', null, null, Translation::get('SelectSpecificEntities'), self::RIGHT_OPTION_SELECT,
            array('class' => 'entity_option_selected')
        );

        $this->addGroup(
            $group, self::PROPERTY_RIGHT_OPTION, Translation::get('PublishFor', null, Utilities::COMMON_LIBRARIES), ''
        );

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
            'html', ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Core\Admin\Announcement', true) . 'RightsForm.js'
        )
        );
    }

    /**
     * Builds the update form with the update button
     */
    public function build_update_form()
    {
        $this->build_form();

        $buttons[] = $this->createElement(
            'style_submit_button', self::PARAM_SUBMIT, Translation::get('Update', null, Utilities::COMMON_LIBRARIES),
            null, null, 'arrow-right'
        );
        $buttons[] = $this->createElement(
            'style_reset_button', self::PARAM_RESET, Translation::get('Reset', null, Utilities::COMMON_LIBRARIES)
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Sets the default values of the form.
     * By default the publication is for everybody who has access to the tool and
     * the publication will be available forever.
     */
    public function setDefaults()
    {
        $defaults = array();

        $defaults[self::PROPERTY_FOREVER] = 1;
        $defaults[self::PROPERTY_RIGHT_OPTION] = self::RIGHT_OPTION_ALL;

        parent::setDefaults($defaults);
    }

    public function setPublicationDefaults(User $user, Publication $publication, array $targetUsersAndGroups)
    {
        $defaults = array();

        if ($publication->get_from_date() != 0)
        {
            $defaults[self::PROPERTY_FOREVER] = 0;
            $defaults[Publication::PROPERTY_FROM_DATE] = $publication->get_from_date();
            $defaults[Publication::PROPERTY_TO_DATE] = $publication->get_to_date();
        }

        $defaults[Publication::PROPERTY_HIDDEN] = $publication->is_hidden();

        if (key_exists(0, $targetUsersAndGroups))
        {
            $defaults[self::PROPERTY_RIGHT_OPTION] = self::RIGHT_OPTION_ALL;
        }
        else
        {
            $hasUserEntities = key_exists(UserEntity::ENTITY_TYPE, $targetUsersAndGroups);
            $hasGroupEntites = key_exists(PlatformGroupEntity::ENTITY_TYPE, $targetUsersAndGroups);
            $hasOnlyOneUserEntity = count($targetUsersAndGroups[UserEntity::ENTITY_TYPE]) == 1;
            $currentUserIsOnlyUserEntity = $targetUsersAndGroups[UserEntity::ENTITY_TYPE][0] == $user->getId();

            if ($hasUserEntities && !$hasGroupEntites && $hasOnlyOneUserEntity && $currentUserIsOnlyUserEntity)
            {
                $defaults[self::PROPERTY_RIGHT_OPTION] = self::RIGHT_OPTION_ME;
            }
            else
            {
                $defaults[self::PROPERTY_RIGHT_OPTION] = self::RIGHT_OPTION_SELECT;
            }

            $defaultElements = new AdvancedElementFinderElements();

            foreach ($targetUsersAndGroups as $targetUsersAndGroupType => $targetUsersAndGroupIdentifiers)
            {
                $entity = $this->entities[$targetUsersAndGroupType];

                foreach ($targetUsersAndGroupIdentifiers as $targetUsersAndGroupIdentifier)
                {
                    $defaultElements->add_element(
                        $entity->get_element_finder_element($targetUsersAndGroupIdentifier)
                    );
                }
            }

            $element = $this->getElement(self::PROPERTY_TARGETS);
            $element->setDefaultValues($defaultElements);
        }

        parent::setDefaults($defaults);
    }
}
