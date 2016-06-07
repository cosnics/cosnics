<?php

namespace Chamilo\Core\Home\Rights\Form;

use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
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
     * Constructor
     *
     * TargetEntitiesForm constructor.
     *
     * @param Block $block
     * @param string $action
     */
    public function __construct(Block $block, $action)
    {
        $this->formName = sprintf('home_block_%s_target_entities_form', $block->getId());
        parent::__construct($this->formName);

        $this->buildForm();
    }

    /**
     * Builds the form
     */
    protected function buildForm()
    {
        $types = new AdvancedElementFinderElementTypes();
        $types->add_element_type(UserEntity::get_element_finder_type());
        $types->add_element_type(PlatformGroupEntity::get_element_finder_type());
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

}