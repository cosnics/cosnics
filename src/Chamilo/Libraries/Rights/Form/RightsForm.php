<?php
namespace Chamilo\Libraries\Rights\Form;

use Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\Rights\Form
 * @author  Sven Vanpoucke
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsForm extends FormValidator
{
    public const INHERIT_FALSE = 1;
    public const INHERIT_TRUE = 0;

    public const PROPERTY_BUTTONS = 'buttons';
    public const PROPERTY_INHERIT = 'inherit';
    public const PROPERTY_RESET = 'reset';
    public const PROPERTY_RIGHT_OPTION = 'right_option';
    public const PROPERTY_SUBMIT = 'submit';
    public const PROPERTY_TARGETS = 'targets';

    public const RIGHT_OPTION_ALL = 0;
    public const RIGHT_OPTION_ME = 1;
    public const RIGHT_OPTION_SELECT = 2;

    /**
     * @var int
     */
    private $availableRights;

    /**
     * @var \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider[]
     */
    private $entities;

    /**
     * @var bool
     */
    private $isAllowedToInherit;

    /**
     * @param string $postBackUrl
     * @param \Symfony\Component\Translation\Translator $translator
     * @param bool $isAllowedToInherit
     * @param int $availableRights
     * @param \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider[] $entities
     *
     * @throws \Exception
     */
    public function __construct(
        string $postBackUrl, bool $isAllowedToInherit, array $availableRights, array $entities
    )
    {
        parent::__construct('simple_rights_editor', self::FORM_METHOD_POST, $postBackUrl);

        $this->isAllowedToInherit = $isAllowedToInherit;
        $this->entities = $entities;
        $this->availableRights = $availableRights;

        $this->buildForm();
        $this->setDefaults();
    }

    /**
     * Builds the form
     */
    protected function buildForm()
    {
        $this->buildInheritanceForm();

        $this->addElement('html', '<div style="display:none;" class="specific_rights_selector_box">');

        foreach ($this->getAvailableRights() as $rightName => $rightIdentifier)
        {
            $this->buildRightForm($rightName, $rightIdentifier);
        }

        $this->addElement('html', '</div>');

        $this->buildFormFooter();
    }

    /**
     * Builds the form footer
     */
    protected function buildFormFooter()
    {
        $translator = $this->getTranslator();

        $buttons = [];

        $buttons[] = $this->createElement(
            'style_submit_button', self::PROPERTY_SUBMIT, $translator->trans('Submit', [], StringUtilities::LIBRARIES),
            null, null, new FontAwesomeGlyph('arrow-right')
        );

        $buttons[] = $this->createElement(
            'style_reset_button', self::PROPERTY_RESET, $translator->trans('Reset', [], StringUtilities::LIBRARIES)
        );

        $this->addGroup($buttons, self::PROPERTY_BUTTONS, null, '&nbsp;', false);

        $this->addElement(
            'html', $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath('Chamilo\Core\Rights\Editor') . 'RightsForm.js'
        )
        );
    }

    /**
     * Builds the inheritance form (wheter to inherit the rights from parent location or not)
     */
    protected function buildInheritanceForm()
    {
        $translator = $this->getTranslator();

        $this->addElement('category', $translator->trans('Inheritance', [], 'Chamilo\Libraries\Rights'));

        $group = [];

        if ($this->isAllowedToInherit())
        {
            $group[] = $this->createElement(
                'radio', null, null, $translator->trans('InheritRights', [], 'Chamilo\Libraries\Rights'),
                self::INHERIT_TRUE, ['class' => 'inherit_rights_selector']
            );
        }
        else
        {
            $group[] = $this->createElement(
                'radio', null, null, $translator->trans('InheritRights', [], 'Chamilo\Libraries\Rights'),
                self::INHERIT_TRUE, ['class' => 'inherit_rights_selector', 'disabled' => 'disabled']
            );
        }

        $group[] = $this->createElement(
            'radio', null, null, $translator->trans('UseSpecificRights', [], 'Chamilo\Libraries\Rights'),
            self::INHERIT_FALSE, ['class' => 'specific_rights_selector']
        );

        $this->addGroup($group, self::PROPERTY_INHERIT, null, '');
    }

    /**
     * Builds the form for a given right
     *
     * @param string $rightName
     * @param int $rightIdentifier
     */
    protected function buildRightForm($rightName, $rightIdentifier)
    {
        $translator = $this->getTranslator();

        $name = self::PROPERTY_RIGHT_OPTION . '[' . $rightIdentifier . ']';

        $this->addElement('category', $rightName);
        $this->addElement('html', '<div class="right">');

        $group = [];

        $group[] = &$this->createElement(
            'radio', null, null, $translator->trans('Everyone', [], 'Chamilo\Libraries\Rights'), self::RIGHT_OPTION_ALL,
            ['class' => 'other_option_selected']
        );
        $group[] = &$this->createElement(
            'radio', null, null, $translator->trans('OnlyForMe', [], 'Chamilo\Libraries\Rights'), self::RIGHT_OPTION_ME,
            ['class' => 'other_option_selected']
        );
        $group[] = &$this->createElement(
            'radio', null, null, $translator->trans('SelectSpecificEntities', [], 'Chamilo\Libraries\Rights'),
            self::RIGHT_OPTION_SELECT, ['class' => 'entity_option_selected']
        );

        $this->addGroup($group, $name, '', '');

        // Add the advanced element finder
        $types = new AdvancedElementFinderElementTypes();

        foreach ($this->getEntities() as $entity)
        {
            $types->add_element_type($entity->getEntityElementFinderType());
        }

        $this->addElement('html', '<div style="display:none;" class="entity_selector_box">');
        $this->addElement(
            'advanced_element_finder', self::PROPERTY_TARGETS . '[' . $rightIdentifier . ']', null, $types
        );

        $this->addElement('html', '</div></div>');
    }

    /**
     * @return int
     */
    public function getAvailableRights(): array
    {
        return $this->availableRights;
    }

    /**
     * @return \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider[]
     */
    public function getEntities(): array
    {
        return $this->entities;
    }

    /**
     * @param int $entityType
     *
     * @return \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider
     */
    public function getEntityByType(int $entityType)
    {
        $entities = $this->getEntities();

        return $entities[$entityType];
    }

    /**
     * @return bool
     */
    public function isAllowedToInherit(): bool
    {
        return $this->isAllowedToInherit;
    }

    /**
     * @param string[] $defaultValues
     * @param string[] $filter
     *
     * @throws \Exception
     */
    public function setDefaults($defaultValues = null, $filter = null)
    {
        if ($this->isAllowedToInherit())
        {
            $defaultValues[self::PROPERTY_INHERIT] = self::INHERIT_TRUE;
        }
        else
        {
            $defaultValues[self::PROPERTY_INHERIT] = self::INHERIT_FALSE;
        }

        parent::setDefaults($defaultValues, $filter);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param int $rightIdentifier
     * @param int $targetEntities
     *
     * @throws \Exception
     */
    public function setRightDefaults(User $user, int $rightIdentifier, array $targetEntities = [])
    {
        $defaults = [];

        if (key_exists(0, $targetEntities))
        {
            $defaults[self::PROPERTY_RIGHT_OPTION][$rightIdentifier] = self::RIGHT_OPTION_ALL;
        }
        else
        {
            $hasUserTargets = key_exists(
                UserEntityProvider::ENTITY_TYPE, $targetEntities
            );
            $hasOnlyOneTargetEntityType = count($targetEntities) == 1;
            $hasOnlyOneTargetUserEntity = is_array($targetEntities[UserEntityProvider::ENTITY_TYPE]) && count(
                    $targetEntities[UserEntityProvider::ENTITY_TYPE]
                ) == 1;
            $currentUserIsOnlyTargetUserEntity = $targetEntities[UserEntityProvider::ENTITY_TYPE][0] == $user->getId();

            if ($hasUserTargets && $hasOnlyOneTargetEntityType && $hasOnlyOneTargetUserEntity &&
                $currentUserIsOnlyTargetUserEntity)
            {
                $defaults[self::PROPERTY_RIGHT_OPTION][$rightIdentifier] = self::RIGHT_OPTION_ME;
            }
            else
            {
                $defaults[self::PROPERTY_RIGHT_OPTION][$rightIdentifier] = self::RIGHT_OPTION_SELECT;
            }

            $defaultElements = new AdvancedElementFinderElements();

            foreach ($targetEntities as $rightTargetEntityType => $rightTargetEntityIdentifiers)
            {
                $entity = $this->getEntityByType($rightTargetEntityType);

                foreach ($rightTargetEntityIdentifiers as $rightTargetEntityIdentifier)
                {
                    $defaultElements->add_element(
                        $entity->getEntityElementFinderElement($rightTargetEntityIdentifier)
                    );
                }
            }

            $element = $this->getElement(self::PROPERTY_TARGETS . '[' . $rightIdentifier . ']');
            $element->setDefaultValues($defaultElements);
        }

        parent::setDefaults($defaults);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param bool $rightsLocationInherits
     * @param int $targetEntities
     *
     * @throws \Exception
     */
    public function setRightsDefaults(User $user, bool $rightsLocationInherits, array $targetEntities)
    {
        $defaults = [];

        if ($rightsLocationInherits)
        {
            $defaults[self::PROPERTY_INHERIT] = self::INHERIT_TRUE;

            foreach ($this->getAvailableRights() as $rightIdentifier)
            {
                $defaults[self::PROPERTY_RIGHT_OPTION][$rightIdentifier] = self::RIGHT_OPTION_ALL;
            }
        }
        else
        {
            $defaults[self::PROPERTY_INHERIT] = self::INHERIT_FALSE;
        }

        parent::setDefaults($defaults);

        foreach ($this->getAvailableRights() as $rightIdentifier)
        {
            $this->setRightDefaults($user, $rightIdentifier, $targetEntities[$rightIdentifier]);
        }
    }
}
