<?php
namespace Chamilo\Core\Home\Rights\Form;

use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Base Target Entities Form
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class TargetEntitiesForm extends FormValidator
{
    const PROPERTY_RIGHTS = 'rights';

    /**
     *
     * @var string
     */
    protected $formName;

    /**
     * The entities
     *
     * @var RightsEntity[]
     */
    protected $entities;

    /**
     * Constructor
     * TargetEntitiesForm constructor.
     *
     * @param string $formName ;
     * @param string $action
     */
    public function __construct($formName, $action)
    {
        parent::__construct($formName, 'POST', $action);

        $this->formName = $formName;

        $this->entities = array(
            UserEntity::ENTITY_TYPE => UserEntity::getInstance(),
            PlatformGroupEntity::ENTITY_TYPE => PlatformGroupEntity::getInstance()
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
            'advanced_element_finder', $this->formName . '_rights', Translation::get('SelectTargetUsersGroups'), $types
        );

        $this->addElement('html', '<div style="margin-top: 20px;"></div>');

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', Translation::get('Save', null, Utilities::COMMON_LIBRARIES), null, null,
            new FontAwesomeGlyph('save')
        );

        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', Translation::get('Reset', null, Utilities::COMMON_LIBRARIES)
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
        $selectedEntities = $this->getSelectedEntities();

        $default_elements = new AdvancedElementFinderElements();

        foreach ($selectedEntities as $selectedEntity)
        {
            $entity = $this->entities[$selectedEntity->get_entity_type()];

            $default_elements->add_element($entity->get_element_finder_element($selectedEntity->get_entity_id()));
        }

        $element = $this->getElement($this->formName . '_rights');
        $element->setDefaultValues($default_elements);

        parent::setDefaults($defaults);
    }

    /**
     * Returns the target entities for the form
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getTargetEntities()
    {
        return $this->exportValue($this->formName . '_rights');
    }

    /**
     * Returns the selected entities
     *
     * @return HomeTargetEntity[]
     */
    abstract protected function getSelectedEntities();
}