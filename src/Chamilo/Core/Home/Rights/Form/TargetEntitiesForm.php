<?php

namespace Chamilo\Core\Home\Rights\Form;

use Chamilo\Core\Home\Rights\Service\ElementRightsService;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\RightsEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Target Entities Form
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TargetEntitiesForm extends FormValidator
{
    const PROPERTY_RIGHTS = 'rights';

    /**
     * @var string
     */
    protected $formName;

    /**
     * @var ElementRightsService
     */
    protected $elementRightsService;

    /**
     * The current element
     *
     * @var Element
     */
    protected $element;

    /**
     * The entities
     *
     * @var RightsEntity[]
     */
    protected $entities;

    /**
     * Constructor
     *
     * TargetEntitiesForm constructor.
     *
     * @param Element $element
     * @param string $action
     * @param ElementRightsService $elementRightsService
     */
    public function __construct(Element $element, $action, ElementRightsService $elementRightsService)
    {
        $this->formName = sprintf('home_block_%s_target_entities_form', $element->getId());
        parent::__construct($this->formName);

        $this->element = $element;
        $this->elementRightsService = $elementRightsService;

        $this->entities = array(
            UserEntity::ENTITY_TYPE => UserEntity::get_instance(),
            PlatformGroupEntity::ENTITY_TYPE => PlatformGroupEntity::get_instance()
        );

        $this->buildForm();
        $this->setDefaults();
    }

    /**
     * Builds the form
     */
    protected function buildForm()
    {
        $types = new AdvancedElementFinderElementTypes();

        foreach ($this->entities as $entity)
        {
            $types->add_element_type($entity->get_element_finder_type());
        }

        $this->addElement(
            'advanced_element_finder',
            $this->formName . '_rights',
            Translation::get('SelectTargetUsersGroups'),
            $types
        );

        $this->addElement('html', '<div style="margin-top: 20px;"></div>');

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation::get('Save', null, Utilities::COMMON_LIBRARIES),
            null,
            null,
            'save'
        );
        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES)
        );
        $buttons[] = $this->createElement(
            'style_submit_button',
            'cancel',
            Translation::get('Cancel', null, Utilities::COMMON_LIBRARIES),
            array('class' => 'btn-danger'),
            null,
            'remove'
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Sets the default values
     *
     * @param array $defaults
     *
     * @throws \Exception
     */
    public function setDefaults($defaults = array())
    {
        $selectedEntities = $this->elementRightsService->getTargetEntitiesForElement($this->element);

        $default_elements = new AdvancedElementFinderElements();

        foreach ($selectedEntities as $selectedEntity)
        {
            $entity = $this->entities[$selectedEntity->get_entity_type()];

            $default_elements->add_element(
                $entity->get_element_finder_element($selectedEntity->get_entity_id())
            );
        }

        $element = $this->getElement($this->formName . '_rights');
        $element->setDefaultValues($default_elements);

        parent::setDefaults($defaults);
    }

}